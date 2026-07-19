<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankAction;
use App\Models\TankDeviceState;
use Illuminate\Http\Request;

class TankDeviceStateController extends Controller
{
    private const PH_DOSE_KEYS = [
        'ph_up',
        'ph_down',
    ];

    private const PH_DOSE_COOLDOWN_SECONDS = 5;

    private const ALLOWED_KEYS = [
        'topup',
        'feeder',
    ];

    public function index(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        $states = TankDeviceState::where('tank_id', $tank->id)->get();

        $mapped = $states->mapWithKeys(function ($state) {
            return [$state->device_key => $state->state];
        });

        $allStates = [];
        foreach (self::ALLOWED_KEYS as $key) {
            $allStates[$key] = (bool) ($mapped[$key] ?? false);
        }

        return response()->json([
            'states' => $allStates,
        ]);
    }

    public function update(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        $data = $request->validate([
            'device_key' => 'required|string',
            'state' => 'required|boolean',
        ]);

        if (!in_array($data['device_key'], self::ALLOWED_KEYS, true)) {
            return response()->json(['error' => 'Invalid device.'], 422);
        }

        TankDeviceState::updateOrCreate(
            ['tank_id' => $tank->id, 'device_key' => $data['device_key']],
            ['state' => $data['state']]
        );

        TankAction::create([
            'tank_id' => $tank->id,
            'user_id' => $request->user()->id,
            'action' => $data['device_key'] . ' ' . ($data['state'] ? 'on' : 'off'),
        ]);

        return response()->json([
            'status' => 'ok',
            'states' => $this->statesForTank($tank),
        ]);
    }

    public function dose(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        if (($tank->control_mode ?? 'auto') === 'auto') {
            return response()->json(['error' => 'Auto mode is enabled. Switch to manual before dosing pH.'], 422);
        }

        $data = $request->validate([
            'device_key' => 'required|string|in:ph_up,ph_down',
        ]);

        $pending = TankAction::where('tank_id', $tank->id)
            ->whereIn('action_type', self::PH_DOSE_KEYS)
            ->whereIn('status', ['pending', 'dispatched'])
            ->first();

        if ($pending) {
            return response()->json([
                'error' => 'A pH dose command is already pending. Wait for the ESP32 to complete it.',
            ], 422);
        }

        $recentSameDose = TankAction::where('tank_id', $tank->id)
            ->where('action_type', $data['device_key'])
            ->where('requested_at', '>=', now()->subSeconds(self::PH_DOSE_COOLDOWN_SECONDS))
            ->exists();

        if ($recentSameDose) {
            return response()->json([
                'error' => 'Please wait a few seconds before requesting another pH dose.',
            ], 429);
        }

        $label = $data['device_key'] === 'ph_up' ? 'pH Up' : 'pH Down';

        $command = TankAction::create([
            'tank_id' => $tank->id,
            'user_id' => $request->user()->id,
            'requested_by' => $request->user()->id,
            'action' => $data['device_key'] . ' dose',
            'action_type' => $data['device_key'],
            'note' => 'One-time manual dose requested from dashboard.',
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => "One {$label} dose has been requested.",
            'command_id' => $command->id,
        ]);
    }

    private function authorizeTank(Tank $tank, Request $request): void
    {
        if ($tank->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function statesForTank(Tank $tank): array
    {
        $mapped = TankDeviceState::where('tank_id', $tank->id)
            ->pluck('state', 'device_key');

        $states = [];
        foreach (self::ALLOWED_KEYS as $key) {
            $states[$key] = (bool) $mapped->get($key, false);
        }

        return $states;
    }
}
