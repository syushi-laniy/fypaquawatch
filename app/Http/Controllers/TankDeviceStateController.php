<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankAction;
use App\Models\TankDeviceState;
use Illuminate\Http\Request;

class TankDeviceStateController extends Controller
{
    private const ALLOWED_KEYS = [
        'ph_up',
        'ph_down',
        'dosing',
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

        return response()->json(['status' => 'ok']);
    }

    private function authorizeTank(Tank $tank, Request $request): void
    {
        if ($tank->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}
