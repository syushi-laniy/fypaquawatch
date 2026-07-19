<?php

namespace App\Http\Controllers;

use App\Models\TankRequest;
use Illuminate\Http\Request;

class TankRequestController extends Controller
{
    public function create()
    {
        return view('user.tank-requests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tank_name' => 'required|string|max:255',
            'tank_size' => 'required|string|max:255',
            'fish_species' => 'nullable|string|max:255',
            'delivery_address' => 'required|string|max:2000',
            'phone_number' => 'required|string|max:50',
            'additional_notes' => 'nullable|string|max:2000',
        ]);

        TankRequest::create($data + [
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return redirect()->route('tanks.index')->with('success', 'Tank request submitted.');
    }

    public function show(Request $request, TankRequest $tankRequest)
    {
        $this->authorizeRequest($tankRequest, $request);

        return view('user.tank-requests.show', compact('tankRequest'));
    }

    private function authorizeRequest(TankRequest $tankRequest, Request $request): void
    {
        if ($tankRequest->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}
