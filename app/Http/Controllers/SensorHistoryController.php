<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankReading;
use App\Models\TankThreshold;
use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SensorHistoryController extends Controller
{
    private const PARAMETERS = ['pH', 'Turbidity', 'Water Level'];

    public function index(Request $request)
    {
        $selectedTank = $this->resolveSelectedTank($request);
        $selectedParameter = $request->query('parameter', 'all');
        if ($selectedParameter !== 'all' && !in_array($selectedParameter, self::PARAMETERS, true)) {
            $selectedParameter = 'all';
        }

        $parameters = $selectedParameter === 'all'
            ? self::PARAMETERS
            : [$selectedParameter];
        $thresholds = Threshold::whereIn('parameter', self::PARAMETERS)->get()->keyBy('parameter');

        if (!$selectedTank) {
            return view('sensor-history.index', [
                'selectedTank' => null,
                'selectedParameter' => $selectedParameter,
                'parameters' => self::PARAMETERS,
                'historyRows' => collect(),
                'chartLabels' => [],
                'chartDatasets' => [],
                'isDemo' => false,
            ]);
        }

        $readings = TankReading::where('tank_id', $selectedTank->id)
            ->whereIn('parameter', $parameters)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->limit(150)
            ->get();

        $isDemo = $readings->isEmpty();
        $historyRows = $isDemo
            ? $this->demoRows($parameters, $thresholds, $selectedTank)
            : $this->realRows($selectedTank, $readings, $thresholds);

        $sortedHistoryRows = $historyRows->sortByDesc('recorded_at')->values();
        $historyRowsPage = $this->paginateRows($request, $sortedHistoryRows, 10);

        [$chartLabels, $chartDatasets] = $this->chartData($historyRows, $parameters);

        return view('sensor-history.index', [
            'selectedTank' => $selectedTank,
            'selectedParameter' => $selectedParameter,
            'parameters' => self::PARAMETERS,
            'historyRows' => $historyRowsPage,
            'chartLabels' => $chartLabels,
            'chartDatasets' => $chartDatasets,
            'isDemo' => $isDemo,
        ]);
    }

    private function resolveSelectedTank(Request $request): ?Tank
    {
        $tankId = $request->session()->get('selected_tank_id');
        $query = Tank::where('user_id', $request->user()->id);

        $tank = $tankId ? (clone $query)->where('id', $tankId)->first() : null;
        if (!$tank) {
            $tank = $query->orderBy('id')->first();
        }

        if ($tank) {
            $request->session()->put('selected_tank_id', $tank->id);
        } else {
            $request->session()->forget('selected_tank_id');
        }

        return $tank;
    }

    private function paginateRows(Request $request, Collection $rows, int $perPage): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $rows->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function realRows(Tank $tank, Collection $readings, Collection $thresholds): Collection
    {
        $tankThresholds = TankThreshold::where('tank_id', $tank->id)
            ->whereIn('parameter', self::PARAMETERS)
            ->get()
            ->keyBy('parameter');
        $speciesPhRange = $this->speciesPhRangeForTank($tank);

        return $readings->map(function (TankReading $reading) use ($tank, $thresholds, $tankThresholds, $speciesPhRange) {
            $value = is_numeric($reading->value) ? (float) $reading->value : null;
            $displayValue = $reading->value;

            if ($reading->parameter === 'Water Level' && $value !== null) {
                $value = $tank->actualWaterLevelFromDistance($value);
                $displayValue = number_format($value, 2);
            }

            return [
                'recorded_at' => $reading->recorded_at ?? $reading->created_at,
                'parameter' => $reading->parameter,
                'value' => $value,
                'display_value' => $displayValue,
                'unit' => $reading->unit ?: $this->defaultUnit($reading->parameter),
                'status' => $this->statusFor($reading->parameter, $value, $thresholds, $tank, $tankThresholds, $speciesPhRange),
            ];
        });
    }

    private function demoRows(array $parameters, Collection $thresholds, ?Tank $tank = null): Collection
    {
        $tankThresholds = $tank
            ? TankThreshold::where('tank_id', $tank->id)->whereIn('parameter', self::PARAMETERS)->get()->keyBy('parameter')
            : collect();
        $speciesPhRange = $tank ? $this->speciesPhRangeForTank($tank) : null;

        $samples = [
            'pH' => [7.0, 7.1, 7.2, 7.3, 7.2, 7.4, 7.3, 7.2],
            'Turbidity' => [4, 5, 6, 5, 4, 7, 5, 4],
            'Water Level' => [17.8, 17.5, 17.1, 16.8, 16.4, 16.9, 17.3, 17.7],
        ];

        $rows = collect();
        foreach ($parameters as $parameter) {
            foreach ($samples[$parameter] as $index => $value) {
                $recordedAt = now()->subHours((count($samples[$parameter]) - 1 - $index) * 3);
                $rows->push([
                    'recorded_at' => $recordedAt,
                    'parameter' => $parameter,
                    'value' => (float) $value,
                    'display_value' => $value,
                    'unit' => $this->defaultUnit($parameter),
                    'status' => $this->statusFor($parameter, (float) $value, $thresholds, $tank, $tankThresholds, $speciesPhRange),
                ]);
            }
        }

        return $rows;
    }

    private function statusFor(
        string $parameter,
        ?float $value,
        Collection $thresholds,
        ?Tank $tank = null,
        ?Collection $tankThresholds = null,
        ?array $speciesPhRange = null
    ): string
    {
        if ($value === null) {
            return 'Warning';
        }

        if ($parameter === 'pH' && $speciesPhRange) {
            return $value >= $speciesPhRange['min'] && $value <= $speciesPhRange['max']
                ? 'Good'
                : 'Warning';
        }

        if ($parameter === 'Water Level' && $tank) {
            $waterThreshold = $tankThresholds?->get($parameter);
            $range = $tank->waterLevelRange(
                is_numeric($waterThreshold?->min_value) ? (float) $waterThreshold->min_value : null,
                is_numeric($waterThreshold?->max_value) ? (float) $waterThreshold->max_value : null
            );

            return $value >= $range['min'] && $value <= $range['max'] ? 'Good' : 'Warning';
        }

        $defaults = [
            'pH' => [6.5, 7.5],
            'Turbidity' => [0, 5],
            'Water Level' => [15, 20.3],
        ];
        $threshold = $thresholds->get($parameter);
        $min = $threshold && is_numeric($threshold->min_value)
            ? (float) $threshold->min_value
            : $defaults[$parameter][0];
        $max = $threshold && is_numeric($threshold->max_value)
            ? (float) $threshold->max_value
            : $defaults[$parameter][1];

        return $value >= $min && $value <= $max ? 'Good' : 'Warning';
    }

    private function speciesPhRangeForTank(Tank $tank): ?array
    {
        $species = $tank->species()->get();
        if ($species->isEmpty()) {
            return null;
        }

        $min = (float) $species->max('min_ph');
        $max = (float) $species->min('max_ph');

        if ($min > $max) {
            return null;
        }

        return [
            'min' => $min,
            'max' => $max,
        ];
    }

    private function chartData(Collection $rows, array $parameters): array
    {
        $ascending = $rows->sortBy('recorded_at');
        $labels = $ascending
            ->map(fn (array $row) => $row['recorded_at']->format('d M H:i'))
            ->unique()
            ->values();
        $colors = [
            'pH' => '#0f6c85',
            'Turbidity' => '#7f1d1d',
            'Water Level' => '#187044',
        ];

        $datasets = collect($parameters)->map(function (string $parameter) use ($ascending, $labels, $colors) {
            $values = $ascending
                ->where('parameter', $parameter)
                ->mapWithKeys(fn (array $row) => [$row['recorded_at']->format('d M H:i') => $row['value']]);

            return [
                'label' => $parameter,
                'data' => $labels->map(fn (string $label) => $values->get($label))->all(),
                'borderColor' => $colors[$parameter],
                'backgroundColor' => $colors[$parameter],
            ];
        })->values()->all();

        return [$labels->all(), $datasets];
    }

    private function defaultUnit(string $parameter): string
    {
        return match ($parameter) {
            'pH' => 'pH',
            'Turbidity' => 'NTU',
            'Water Level' => 'cm',
            default => '',
        };
    }
}
