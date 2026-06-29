<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parameter;
use App\Models\Threshold;
use Illuminate\Http\Request;

class ThresholdController extends Controller
{
    public function index(Request $request)
    {
        $query = Threshold::query();
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where('parameter', 'like', '%' . $search . '%');
        }

        $thresholds = $query->orderBy('parameter')->get();

        return view('admin.thresholds.index', compact('thresholds'));
    }

    public function create()
    {
        $parameters = Parameter::orderBy('name')->get();

        return view('admin.thresholds.create', compact('parameters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parameter' => 'required|string|max:255',
            'min_value' => 'required|string|max:50',
            'max_value' => 'required|string|max:50',
        ]);

        Threshold::create($data);

        return redirect()->route('thresholds.index')->with('success', 'Threshold created.');
    }

    public function edit(Threshold $threshold)
    {
        $parameters = Parameter::orderBy('name')->get();

        return view('admin.thresholds.edit', compact('threshold', 'parameters'));
    }

    public function update(Request $request, Threshold $threshold)
    {
        $data = $request->validate([
            'parameter' => 'required|string|max:255',
            'min_value' => 'required|string|max:50',
            'max_value' => 'required|string|max:50',
        ]);

        $threshold->update($data);

        return redirect()->route('thresholds.index')->with('success', 'Threshold updated.');
    }

    public function destroy(Threshold $threshold)
    {
        $threshold->delete();

        return redirect()->route('thresholds.index')->with('success', 'Threshold deleted.');
    }
}
