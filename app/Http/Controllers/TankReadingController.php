<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankReading;
use App\Models\TankThreshold;
use App\Models\Parameter;
use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TankReadingController extends Controller
{
    public function store(Request $request, Tank $tank)
    {
        if ($tank->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'readings' => 'required|array',
            'readings.*.parameter' => 'required|string|max:255',
            'readings.*.value' => 'required',
            'readings.*.unit' => 'nullable|string|max:20',
        ]);

        $activeParameters = Parameter::where('status', 'Active')->pluck('name')->flip();
        $thresholds = TankThreshold::where('tank_id', $tank->id)->get()->keyBy('parameter');
        $fallback = Threshold::all()->keyBy('parameter');
        $alerts = [];

        foreach ($data['readings'] as $reading) {
            TankReading::create([
                'tank_id' => $tank->id,
                'parameter' => $reading['parameter'],
                'value' => (string) $reading['value'],
                'unit' => $reading['unit'] ?? null,
                'recorded_at' => now(),
            ]);

            if (!$activeParameters->has($reading['parameter'])) {
                continue;
            }

            $threshold = $thresholds->get($reading['parameter']) ?? $fallback->get($reading['parameter']);
            if (!$threshold) {
                continue;
            }

            $value = (float) $reading['value'];
            $min = (float) $threshold->min_value;
            $max = (float) $threshold->max_value;
            if (!is_numeric($reading['value']) || !is_numeric($threshold->min_value) || !is_numeric($threshold->max_value)) {
                continue;
            }

            if ($value < $min || $value > $max) {
                $action = $this->suggestAction($reading['parameter'], $value < $min ? 'low' : 'high');
                $alerts[] = [
                    'parameter' => $reading['parameter'],
                    'value' => $value,
                    'unit' => $reading['unit'] ?? '',
                    'min' => $min,
                    'max' => $max,
                    'action' => $action,
                ];
            }
        }

        if (!empty($alerts)) {
            $this->sendAlertBatch($tank, $alerts);
        }

        return response()->json(['status' => 'ok']);
    }

    private function suggestAction(string $parameter, string $direction): string
    {
        $key = strtolower($parameter);
        $low = $direction === 'low';

        if ($key === 'ph') {
            return $low ? 'pH Up Dosing' : 'pH Down Dosing';
        }
        if (str_contains($key, 'turbidity')) {
            return 'Alert: Check filter condition';
        }
        if (str_contains($key, 'water level')) {
            return $low ? 'Auto Top-up Valve' : 'Drain/Overflow';
        }

        return 'Check system';
    }

    private function sendAlertBatch(Tank $tank, array $alerts): void
    {
        $user = $tank->user;
        $chatId = $user?->telegram_chat_id;
        if (!$chatId) {
            return;
        }

        $token = config('services.telegram.token');
        if (!$token) {
            return;
        }

        $text = "⚠️ Alert: {$tank->name}\n";
        foreach ($alerts as $alert) {
            $text .= "{$alert['parameter']}: {$alert['value']} {$alert['unit']}\n";
            $text .= "Range: {$alert['min']} - {$alert['max']} {$alert['unit']}\n";
            $text .= "Action: {$alert['action']}\n";
            $text .= "---\n";
        }

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }
}
