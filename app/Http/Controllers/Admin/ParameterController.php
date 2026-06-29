<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parameter;
use Illuminate\Http\Request;

class ParameterController extends Controller
{
    public function index(Request $request)
    {
        $query = Parameter::query();
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $parameters = $query->orderBy('name')->get();

        return view('admin.parameters.index', compact('parameters'));
    }

    public function create()
    {
        return view('admin.parameters.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
        ]);

        $data['status'] = $data['status'] ?? 'Active';

        Parameter::create($data);

        return redirect()->route('parameters.index')->with('success', 'Parameter created.');
    }

    public function edit(Parameter $parameter)
    {
        return view('admin.parameters.edit', compact('parameter'));
    }

    public function update(Request $request, Parameter $parameter)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
        ]);

        $parameter->update($data);

        return redirect()->route('parameters.index')->with('success', 'Parameter updated.');
    }

    public function destroy(Parameter $parameter)
    {
        $parameter->delete();

        return redirect()->route('parameters.index')->with('success', 'Parameter deleted.');
    }
}
