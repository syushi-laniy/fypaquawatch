<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankThreshold;
use App\Models\Parameter;
use Illuminate\Http\Request;

class TankThresholdController extends Controller
{
    public function index(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        $parameters = Parameter::where('status', 'Active')->orderBy('name')->get();
        $activeNames = $parameters->pluck('name')->all();
        $thresholds = TankThreshold::where('tank_id', $tank->id)
            ->whereIn('parameter', $activeNames)
            ->orderBy('parameter')
            ->get();

        return view('user.thresholds.index', compact('tank', 'thresholds', 'parameters'));
    }

    public function store(Request $request, Tank $tank)
    {
        $this->authorizeTank($tank, $request);

        $data = $request->validate([
            'parameter' => 'required|string|max:255',
            'min_value' => 'required|string|max:50',
            'max_value' => 'required|string|max:50',
        ]);

        TankThreshold::updateOrCreate(
            ['tank_id' => $tank->id, 'parameter' => $data['parameter']],
            $data + ['tank_id' => $tank->id]
        );

        return redirect()->route('tanks.thresholds.index', $tank)->with('success', 'Threshold saved.');
    }

    public function edit(Request $request, Tank $tank, TankThreshold $threshold)
    {
        $this->authorizeTank($tank, $request);
        $this->authorizeThreshold($tank, $threshold);

        $parameters = Parameter::where('status', 'Active')
            ->orWhere('name', $threshold->parameter)
            ->orderBy('name')
            ->get();

        return view('user.thresholds.edit', compact('tank', 'threshold', 'parameters'));
    }

    public function update(Request $request, Tank $tank, TankThreshold $threshold)
    {
        $this->authorizeTank($tank, $request);
        $this->authorizeThreshold($tank, $threshold);

        $data = $request->validate([
            'parameter' => 'required|string|max:255',
            'min_value' => 'required|string|max:50',
            'max_value' => 'required|string|max:50',
        ]);

        $threshold->update($data);

        return redirect()->route('tanks.thresholds.index', $tank)->with('success', 'Threshold updated.');
    }

    public function destroy(Request $request, Tank $tank, TankThreshold $threshold)
    {
        $this->authorizeTank($tank, $request);
        $this->authorizeThreshold($tank, $threshold);

        $threshold->delete();

        return redirect()->route('tanks.thresholds.index', $tank)->with('success', 'Threshold deleted.');
    }

    private function authorizeTank(Tank $tank, Request $request): void
    {
        if ($tank->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function authorizeThreshold(Tank $tank, TankThreshold $threshold): void
    {
        if ($threshold->tank_id !== $tank->id) {
            abort(404);
        }
    }
}
