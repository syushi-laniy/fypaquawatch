<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankAction;
use App\Models\TankDeviceState;
use App\Models\TankReading;
use App\Models\TankThreshold;
use App\Models\Threshold;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tank = $this->resolveSelectedTank($request);

        if (!$tank) {
            return $this->renderDashboard(null);
        }

        return $this->renderDashboard($tank);
    }

    public function selectTank(Request $request)
    {
        $data = $request->validate([
            'tank_id' => 'required|integer',
        ]);

        $tank = Tank::where('user_id', $request->user()->id)
            ->where('id', $data['tank_id'])
            ->firstOrFail();

        $request->session()->put('selected_tank_id', $tank->id);

        return redirect()->route('dashboard');
    }

    public function show(Request $request, Tank $tank)
    {
        if ($tank->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->session()->put('selected_tank_id', $tank->id);

        return $this->renderDashboard($tank);
    }

    private function resolveSelectedTank(Request $request): ?Tank
    {
        $selectedTankId = $request->session()->get('selected_tank_id');

        if ($selectedTankId) {
            $tank = Tank::where('user_id', $request->user()->id)
                ->where('id', $selectedTankId)
                ->first();

            if ($tank) {
                return $tank;
            }

            $request->session()->forget('selected_tank_id');
        }

        $tank = Tank::where('user_id', $request->user()->id)
            ->orderBy('id')
            ->first();

        if ($tank) {
            $request->session()->put('selected_tank_id', $tank->id);
        }

        return $tank;
    }

    private function renderDashboard(?Tank $tank)
    {
        if (!$tank) {
            return view('user.dashboard', [
                'selectedTank' => null,
                'thresholds' => collect(),
                'latestReadings' => collect(),
                'sensorReadings' => collect(),
                'actions' => collect(),
                'deviceStates' => [],
                'activeParameters' => [],
            ]);
        }

        $sensorDefinitions = $this->sensorDefinitions();
        $parameters = array_keys($sensorDefinitions);
        $thresholds = Threshold::whereIn('parameter', $parameters)->get()->keyBy('parameter');
        $tankThresholds = TankThreshold::where('tank_id', $tank->id)
            ->whereIn('parameter', $parameters)
            ->get()
            ->keyBy('parameter');
        $latestReadings = collect();
        $tankSpecies = $tank->species()->get();
        $speciesPhRange = $tankSpecies->isNotEmpty()
            ? $this->calculateSharedSpeciesPhRange($tankSpecies)
            : null;

        $sensorReadings = collect($sensorDefinitions)->map(function (array $definition, string $parameter) use ($tank, $thresholds, $tankThresholds, $speciesPhRange, &$latestReadings) {
            $reading = TankReading::where('tank_id', $tank->id)
                ->where('parameter', $parameter)
                ->orderByDesc('recorded_at')
                ->orderByDesc('id')
                ->first();

            if ($reading) {
                $latestReadings->put($parameter, $reading);
            }

            $threshold = $tankThresholds->get($parameter) ?? $thresholds->get($parameter);
            $rawValue = $reading ? (float) $reading->value : null;
            $value = $rawValue ?? $definition['demo'];
            $unit = $reading && $reading->unit ? $reading->unit : $definition['unit'];
            $min = $threshold && is_numeric($threshold->min_value) ? (float) $threshold->min_value : $definition['min'];
            $max = $threshold && is_numeric($threshold->max_value) ? (float) $threshold->max_value : $definition['max'];
            $gaugeMax = $definition['gauge_max'];
            $rangeSource = 'Default Threshold';

            if ($parameter === 'pH' && $speciesPhRange) {
                $min = $speciesPhRange['min'];
                $max = $speciesPhRange['max'];
                $rangeSource = 'Species pH Range';
            }

            if ($parameter === 'Water Level') {
                $waterThreshold = $tankThresholds->get($parameter);
                $range = $tank->waterLevelRange(
                    is_numeric($waterThreshold?->min_value) ? (float) $waterThreshold->min_value : null,
                    is_numeric($waterThreshold?->max_value) ? (float) $waterThreshold->max_value : null
                );

                $min = $range['min'];
                $max = $range['max'];
                $gaugeMax = $tank->tankHeightCm();
                $value = $rawValue !== null
                    ? $tank->actualWaterLevelFromDistance($rawValue)
                    : min($definition['demo'], $gaugeMax);
                $rangeSource = $waterThreshold ? 'Tank Water Level Range' : 'Tank Height Range';
            }

            return [
                'key' => $parameter,
                'title' => $definition['title'],
                'value' => $value,
                'unit' => $unit,
                'mode' => $reading ? 'Live Data' : 'Demo Mode',
                'status' => $value >= $min && $value <= $max ? 'Good' : 'Warning',
                'min' => $min,
                'max' => $max,
                'gauge_min' => $definition['gauge_min'],
                'gauge_max' => $gaugeMax,
                'device_low' => $definition['device_low'],
                'device_high' => $definition['device_high'],
                'recorded_at' => $reading?->recorded_at,
                'range_source' => $rangeSource,
            ];
        });

        $actions = TankAction::where('tank_id', $tank->id)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $deviceStates = TankDeviceState::where('tank_id', $tank->id)
            ->get()
            ->mapWithKeys(function ($state) {
                return [$state->device_key => $state->state];
            })
            ->all();

        return view('user.dashboard', [
            'selectedTank' => $tank,
            'thresholds' => $thresholds,
            'latestReadings' => $latestReadings,
            'sensorReadings' => $sensorReadings,
            'actions' => $actions,
            'deviceStates' => $deviceStates,
            'activeParameters' => $parameters,
            'tankSpecies' => $tankSpecies,
            'speciesPhRange' => $speciesPhRange,
        ]);
    }

    private function calculateSharedSpeciesPhRange(Collection $species): array
    {
        $min = (float) $species->max('min_ph');
        $max = (float) $species->min('max_ph');

        return [
            'min' => $min,
            'max' => $max,
            'compatible' => $min <= $max,
        ];
    }

    private function sensorDefinitions(): array
    {
        return [
            'pH' => [
                'title' => 'Current pH',
                'unit' => 'pH',
                'demo' => 7.2,
                'min' => 6.5,
                'max' => 7.5,
                'gauge_min' => 0,
                'gauge_max' => 14,
                'device_low' => 'pH Up Dosing',
                'device_high' => 'pH Down Dosing',
            ],
            'Turbidity' => [
                'title' => 'Current Turbidity',
                'unit' => 'NTU',
                'demo' => 15,
                'min' => 0,
                'max' => 5,
                'gauge_min' => 0,
                'gauge_max' => 100,
                'device_low' => 'Alert: Check filter',
                'device_high' => 'Alert: Check filter',
            ],
            'Water Level' => [
                'title' => 'Current Water Level',
                'unit' => 'cm',
                'demo' => 17.5,
                'min' => 15,
                'max' => 20.3,
                'gauge_min' => 0,
                'gauge_max' => 20.3,
                'device_low' => 'Auto Top-up Pump',
                'device_high' => 'Drain/Overflow',
            ],
        ];
    }
}
