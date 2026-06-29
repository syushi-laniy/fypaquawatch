<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tank;
use Illuminate\Http\Request;

class TankController extends Controller
{
    public function index(Request $request)
    {
        $query = Tank::with('user');
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tanks = $query->orderBy('name')->get();

        return view('admin.tanks.index', compact('tanks'));
    }

    public function show(Tank $tank)
    {
        $tank->load('user');

        return view('admin.tanks.show', compact('tank'));
    }
}
