<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tank;
use App\Models\TankRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TankRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = TankRequest::with(['user', 'tank'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tankRequests = $query->get();

        return view('admin.tank-requests.index', compact('tankRequests'));
    }

    public function show(TankRequest $tankRequest)
    {
        $tankRequest->load(['user', 'tank', 'approver']);

        return view('admin.tank-requests.show', compact('tankRequest'));
    }

    public function update(Request $request, TankRequest $tankRequest)
    {
        $rules = [
            'status' => ['required', Rule::in(TankRequest::STATUSES)],
            'tank_code' => ['nullable', 'string', 'max:50', Rule::unique('tank_requests', 'tank_code')->ignore($tankRequest->id)],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ];

        if ($request->input('status') === 'completed') {
            $rules['tank_code'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('tank_requests', 'tank_code')->ignore($tankRequest->id),
                Rule::unique('tanks', 'code')->ignore($tankRequest->tank_id),
            ];
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($request, $tankRequest, $data) {
            $tankRequest->fill([
                'status' => $data['status'],
                'tank_code' => $data['tank_code'] ?? null,
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            if (in_array($data['status'], ['approved', 'in_progress', 'completed'], true) && !$tankRequest->approved_at) {
                $tankRequest->approved_by = $request->user()->id;
                $tankRequest->approved_at = now();
            }

            if ($data['status'] === 'completed') {
                if (!$tankRequest->tank_id) {
                    $tank = Tank::create([
                        'user_id' => $tankRequest->user_id,
                        'name' => $tankRequest->tank_name,
                        'code' => $data['tank_code'],
                        'status' => 'Active',
                        'control_mode' => 'auto',
                    ]);

                    $tankRequest->tank_id = $tank->id;
                } else {
                    $tankRequest->tank?->update([
                        'code' => $data['tank_code'],
                    ]);
                }

                if (!$tankRequest->completed_at) {
                    $tankRequest->completed_at = now();
                }
            }

            $tankRequest->save();
        });

        return redirect()->route('admin.tank-requests.show', $tankRequest)->with('success', 'Tank request updated.');
    }
}
