<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankAction;
use Illuminate\Http\Request;

class TankActionController extends Controller
{
    public function store(Request $request, Tank $tank)
    {
        if ($tank->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'action' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
        ]);

        TankAction::create([
            'tank_id' => $tank->id,
            'user_id' => $request->user()->id,
            'action' => $data['action'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('tanks.dashboard', $tank)->with('success', 'Action recorded.');
    }
}
