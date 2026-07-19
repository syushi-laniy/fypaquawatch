<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tank;
use App\Models\TankAction;
use App\Models\TankDeviceState;
use App\Models\TankReading;
use App\Models\TankThreshold;
use App\Models\Threshold;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class IoTController extends Controller
{
    private const DEVICE_KEYS = [
        'topup',
        'feeder',
    ];

    private const PH_DOSE_KEYS = [
        'ph_up',
        'ph_down',
    ];

    private const PH_DOSE_DURATION_MS = 500;

    public function storeReadings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string|max:50',
            'tank_id' => 'required|integer',
            'readings' => 'required|array|min:1',
            'readings.*.parameter' => 'required|string|max:255',
            'readings.*.value' => 'required',
            'readings.*.unit' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $tank = $this->findTank($data['device_token'], (int) $data['tank_id']);

        if (!$tank) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device token or tank ID.',
            ], 404);
        }

        foreach ($data['readings'] as $reading) {
            TankReading::create([
                'tank_id' => $tank->id,
                'parameter' => $reading['parameter'],
                'value' => (string) $reading['value'],
                'unit' => $reading['unit'] ?? null,
                'recorded_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function commands(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'device_token' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tank = Tank::where('code', $validator->validated()['device_token'])->first();

        if (!$tank) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device token.',
            ], 404);
        }

        $states = TankDeviceState::where('tank_id', $tank->id)
            ->whereIn('device_key', self::DEVICE_KEYS)
            ->pluck('state', 'device_key');

        $response = [
            'control_mode' => $tank->control_mode ?: 'manual',
            'ph_up' => 'off',
            'ph_down' => 'off',
        ];
        foreach (self::DEVICE_KEYS as $key) {
            $response[$key] = $states->get($key, false) ? 'on' : 'off';
        }

        $response['thresholds'] = $this->thresholdsForTank($tank);

        $doseCommand = TankAction::where('tank_id', $tank->id)
            ->whereIn('action_type', self::PH_DOSE_KEYS)
            ->where('status', 'pending')
            ->orderBy('requested_at')
            ->orderBy('id')
            ->first();

        if ($doseCommand) {
            $response[$doseCommand->action_type] = 'dose';
            $response['command_id'] = $doseCommand->id;
            $response['dose_duration_ms'] = self::PH_DOSE_DURATION_MS;

            $doseCommand->update([
                'status' => 'dispatched',
            ]);
        }

        if ($response['feeder'] === 'on') {
            TankDeviceState::where('tank_id', $tank->id)
                ->where('device_key', 'feeder')
                ->update(['state' => false]);
        }

        return response()->json($response);
    }

    public function alert(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string|max:50',
            'tank_id' => 'required|integer',
            'type' => 'required|string|max:100',
            'message' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $tank = $this->findTank($data['device_token'], (int) $data['tank_id']);

        if (!$tank) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device token or tank ID.',
            ], 404);
        }

        TankAction::create([
            'tank_id' => $tank->id,
            'user_id' => $tank->user_id,
            'requested_by' => null,
            'action' => $data['type'],
            'action_type' => $data['type'],
            'note' => $data['message'],
            'status' => 'alert',
            'requested_at' => now(),
            'completed_at' => now(),
        ]);

        $chatId = $tank->user?->telegram_chat_id;
        $token = config('services.telegram.token');

        if ($chatId && $token) {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "AquaWatch Alert: {$tank->name}\n{$data['message']}",
            ]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function acknowledgeCommand(Request $request, TankAction $command): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tank = Tank::where('code', $validator->validated()['device_token'])
            ->where('id', $command->tank_id)
            ->first();

        if (!$tank) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device token for this command.',
            ], 404);
        }

        if (!in_array($command->action_type, self::PH_DOSE_KEYS, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Command is not an IoT dose command.',
            ], 422);
        }

        if ($command->status !== 'completed') {
            $command->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'command_id' => $command->id,
        ]);
    }

    private function findTank(string $deviceToken, int $tankId): ?Tank
    {
        return Tank::where('id', $tankId)
            ->where('code', $deviceToken)
            ->first();
    }

    private function thresholdsForTank(Tank $tank): array
    {
        $tankThresholds = TankThreshold::where('tank_id', $tank->id)->get()->keyBy('parameter');
        $fallbackThresholds = Threshold::all()->keyBy('parameter');

        $phMin = $this->thresholdValue($tankThresholds, $fallbackThresholds, 'pH', 'min_value', 6.4);
        $phMax = $this->thresholdValue($tankThresholds, $fallbackThresholds, 'pH', 'max_value', 7.6);

        $species = $tank->species()->get();
        if ($species->isNotEmpty()) {
            $speciesMin = (float) $species->max('min_ph');
            $speciesMax = (float) $species->min('max_ph');
            if ($speciesMin <= $speciesMax) {
                $phMin = $speciesMin;
                $phMax = $speciesMax;
            }
        }

        $waterThreshold = $tankThresholds->get('Water Level');
        $waterRange = $tank->waterLevelRange(
            is_numeric($waterThreshold?->min_value) ? (float) $waterThreshold->min_value : null,
            is_numeric($waterThreshold?->max_value) ? (float) $waterThreshold->max_value : null
        );
        $minimumWaterLevel = $waterRange['min'];

        return [
            'min_ph' => $phMin,
            'max_ph' => $phMax,
            'max_turbidity' => $this->thresholdValue($tankThresholds, $fallbackThresholds, 'Turbidity', 'max_value', 40.0),
            'min_water' => max($tank->tankHeightCm() - $minimumWaterLevel, 0),
            'min_water_level' => $minimumWaterLevel,
            'tank_height_cm' => $tank->tankHeightCm(),
        ];
    }

    private function thresholdValue($tankThresholds, $fallbackThresholds, string $parameter, string $field, float $default): float
    {
        $threshold = $tankThresholds->get($parameter) ?? $fallbackThresholds->get($parameter);
        $value = $threshold?->{$field};

        return is_numeric($value) ? (float) $value : $default;
    }
}
