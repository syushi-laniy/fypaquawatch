<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankReading;
use Illuminate\Http\Request;

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

        foreach ($data['readings'] as $reading) {
            TankReading::create([
                'tank_id' => $tank->id,
                'parameter' => $reading['parameter'],
                'value' => (string) $reading['value'],
                'unit' => $reading['unit'] ?? null,
                'recorded_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
