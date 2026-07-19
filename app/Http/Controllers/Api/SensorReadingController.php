<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tank;
use App\Models\TankReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SensorReadingController extends Controller
{
    private const STALE_AFTER_SECONDS = 60;

    private const PARAMETERS = [
        'ph' => [
            'labels' => ['ph', 'pH'],
            'unit' => 'pH',
        ],
        'turbidity' => [
            'labels' => ['turbidity', 'Turbidity'],
            'unit' => '%',
        ],
        'water_level' => [
            'labels' => ['water_level', 'water level', 'Water Level', 'water-level'],
            'unit' => 'cm',
        ],
    ];

    public function store(Request $request): JsonResponse
    {
        return app(IoTController::class)->storeReadings($request);
    }

    public function latest(Tank $tank): JsonResponse
    {
        return $this->latestReadings($tank);
    }

    public function latestReadings(Tank $tank): JsonResponse
    {
        $readings = TankReading::where('tank_id', $tank->id)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (TankReading $reading) => $this->normalizeParameter($reading->parameter))
            ->map(fn ($group) => $group->first());

        $freshSensorCount = 0;

        $payload = collect(self::PARAMETERS)
            ->mapWithKeys(function (array $definition, string $key) use ($readings, $tank, &$freshSensorCount) {
                $reading = $readings->get($key);

                if (!$reading) {
                    return [$key => [
                        'value' => null,
                        'unit' => $definition['unit'],
                        'recorded_at' => null,
                        'status' => 'offline',
                        'is_stale' => true,
                        'age_seconds' => null,
                    ]];
                }

                $value = is_numeric($reading->value) ? (float) $reading->value : $reading->value;
                $recordedAt = $reading->recorded_at;
                $ageSeconds = $recordedAt ? max(0, now()->diffInSeconds($recordedAt)) : null;
                $isStale = $ageSeconds === null || $ageSeconds > self::STALE_AFTER_SECONDS;
                $status = $isStale ? 'offline' : 'online';
                $extra = [];

                if (!$isStale) {
                    $freshSensorCount++;
                }

                if ($key === 'water_level' && is_numeric($reading->value)) {
                    $extra = [
                        'raw_distance_cm' => (float) $reading->value,
                        'tank_height_cm' => $tank->tankHeightCm(),
                    ];
                    $value = $tank->actualWaterLevelFromDistance((float) $reading->value);
                }

                return [
                    $key => array_merge([
                        'value' => $value,
                        'unit' => $definition['unit'],
                        'recorded_at' => optional($recordedAt)->toISOString(),
                        'status' => $status,
                        'is_stale' => $isStale,
                        'age_seconds' => $ageSeconds,
                    ], $extra),
                ];
            })
            ->all();

        return response()->json([
            'success' => true,
            'tank_id' => $tank->id,
            'device_status' => $freshSensorCount > 0 ? 'online' : 'offline',
            'stale_after_seconds' => self::STALE_AFTER_SECONDS,
            'readings' => $payload,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    private function normalizeParameter(string $parameter): string
    {
        $normalized = strtolower(trim(str_replace(['-', '_'], ' ', $parameter)));

        return match ($normalized) {
            'ph' => 'ph',
            'turbidity' => 'turbidity',
            'water level' => 'water_level',
            default => $parameter,
        };
    }
}
