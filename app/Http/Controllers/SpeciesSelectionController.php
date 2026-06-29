<?php

namespace App\Http\Controllers;

use App\Models\Species;
use App\Models\Tank;
use Illuminate\Http\Request;

class SpeciesSelectionController extends Controller
{
    public function index(Request $request)
    {
        $tank = $this->resolveSelectedTank($request);
        $species = Species::orderBy('name')->get();
        $selectedSpecies = $tank
            ? $tank->species()->orderBy('name')->get()
            : collect();
        $selectedIds = $selectedSpecies->pluck('id')->all();
        $recommendedRange = $selectedSpecies->isNotEmpty()
            ? $this->calculateSharedPhRange($selectedSpecies)
            : null;

        return view('species-selection.index', compact(
            'tank',
            'species',
            'selectedSpecies',
            'selectedIds',
            'recommendedRange'
        ));
    }

    public function update(Request $request)
    {
        $tank = $this->resolveSelectedTank($request);
        if (!$tank) {
            return redirect()->route('dashboard')->withErrors([
                'tank' => 'Select or add a tank before saving species.',
            ]);
        }

        $data = $request->validate([
            'species_ids' => 'nullable|array',
            'species_ids.*' => 'integer|exists:species,id',
        ]);

        $tank->species()->sync($data['species_ids'] ?? []);

        return redirect()->route('species.index')->with('success', 'Tank species updated.');
    }

    private function resolveSelectedTank(Request $request): ?Tank
    {
        $tankId = $request->session()->get('selected_tank_id');
        if ($tankId) {
            $tank = Tank::where('user_id', $request->user()->id)
                ->where('id', $tankId)
                ->first();
            if ($tank) {
                return $tank;
            }
            $request->session()->forget('selected_tank_id');
        }

        $tank = Tank::where('user_id', $request->user()->id)->orderBy('id')->first();
        if ($tank) {
            $request->session()->put('selected_tank_id', $tank->id);
        }

        return $tank;
    }

    private function calculateSharedPhRange($species): array
    {
        $min = $species->max('min_ph');
        $max = $species->min('max_ph');

        return [
            'min' => $min,
            'max' => $max,
            'compatible' => $min <= $max,
        ];
    }
}
