<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use Illuminate\Http\Request;

class TankController extends Controller
{
    public function index(Request $request)
    {
        $tanks = Tank::where('user_id', $request->user()->id)
            ->orderBy('name')
            ->get();

        return view('user.tanks.index', compact('tanks'));
    }

    public function create()
    {
        return view('user.tanks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'control_mode' => 'nullable|in:auto,manual',
            'ph_sensor' => 'nullable|string|max:255',
            'turbidity_sensor' => 'nullable|string|max:255',
            'water_level_sensor' => 'nullable|string|max:255',
            'dosing_device' => 'nullable|string|max:255',
            'topup_device' => 'nullable|string|max:255',
            'feeder_device' => 'nullable|string|max:255',
        ]);

        $data['status'] = $data['status'] ?? 'Active';
        $data['control_mode'] = $data['control_mode'] ?? 'auto';
        $data['user_id'] = $request->user()->id;

        $tank = Tank::create($data);

        $request->session()->put('selected_tank_id', $tank->id);

        return redirect()->route('tanks.dashboard', $tank)->with('success', 'Tank added.');
    }

    public function show(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        return view('user.tanks.show', compact('tank'));
    }

    public function edit(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        return view('user.tanks.edit', compact('tank'));
    }

    public function update(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'control_mode' => 'nullable|in:auto,manual',
            'ph_sensor' => 'nullable|string|max:255',
            'turbidity_sensor' => 'nullable|string|max:255',
            'water_level_sensor' => 'nullable|string|max:255',
            'dosing_device' => 'nullable|string|max:255',
            'topup_device' => 'nullable|string|max:255',
            'feeder_device' => 'nullable|string|max:255',
        ]);

        $tank->update($data);

        return redirect()->route('tanks.index')->with('success', 'Tank updated.');
    }

    public function destroy(Tank $tank, Request $request)
    {
        $this->authorizeTank($tank, $request);

        $tank->delete();

        return redirect()->route('tanks.index')->with('success', 'Tank deleted.');
    }

    private function authorizeTank(Tank $tank, Request $request): void
    {
        if ($tank->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    public function updateMode(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        $data = $request->validate([
            'control_mode' => 'required|in:auto,manual',
        ]);

        $tank->update([
            'control_mode' => $data['control_mode'],
        ]);

        if ($data['control_mode'] === 'manual') {
            \App\Models\TankDeviceState::where('tank_id', $tank->id)->update(['state' => false]);
        }

        return redirect()->route('tanks.dashboard', $tank)->with('success', 'Control mode updated.');
    }

}
