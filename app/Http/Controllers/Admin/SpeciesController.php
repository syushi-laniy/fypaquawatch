<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpeciesController extends Controller
{
    public function index(Request $request)
    {
        $query = Species::query();
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $species = $query->orderBy('name')->get();

        return view('admin.species.index', compact('species'));
    }

    public function create()
    {
        return view('admin.species.create');
    }

    public function store(Request $request)
    {
        Species::create($this->validatedData($request));

        return redirect()->route('admin.species.index')->with('success', 'Fish species created.');
    }

    public function show(Species $species)
    {
        $species->load('tanks.user');

        return view('admin.species.show', compact('species'));
    }

    public function edit(Species $species)
    {
        return view('admin.species.edit', compact('species'));
    }

    public function update(Request $request, Species $species)
    {
        $species->update($this->validatedData($request, $species));

        return redirect()->route('admin.species.index')->with('success', 'Fish species updated.');
    }

    public function destroy(Species $species)
    {
        $species->delete();

        return redirect()->route('admin.species.index')->with('success', 'Fish species deleted.');
    }

    private function validatedData(Request $request, ?Species $species = null): array
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('species', 'name')->ignore($species?->id),
            ],
            'description' => 'nullable|string|max:2000',
            'min_ph' => 'required|numeric|min:0|max:14',
            'max_ph' => 'required|numeric|min:0|max:14|gte:min_ph',
            'image_path' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
