<?php

namespace App\Http\Controllers;

use App\Models\Species;
use App\Models\Tank;
use Illuminate\Http\Request;

class CommunityCalculatorController extends Controller
{
    public function index()
    {
        return view('community-calculator.index', [
            'species' => Species::orderBy('name')->get(),
            'selectedIds' => [],
            'selectedSpecies' => collect(),
            'result' => null,
        ]);
    }

    public function calculate(Request $request)
    {
        $data = $request->validate([
            'species_ids' => 'required|array|min:1',
            'species_ids.*' => 'integer|exists:species,id',
        ]);

        $selectedSpecies = Species::whereIn('id', $data['species_ids'])
            ->orderBy('name')
            ->get();

        return view('community-calculator.index', [
            'species' => Species::orderBy('name')->get(),
            'selectedIds' => $selectedSpecies->pluck('id')->all(),
            'selectedSpecies' => $selectedSpecies,
            'result' => $this->calculateSharedPhRange($selectedSpecies),
        ]);
    }

    public function apply(Request $request)
    {
        $data = $request->validate([
            'species_ids' => 'required|array|min:1',
            'species_ids.*' => 'integer|exists:species,id',
        ]);

        $tank = $this->resolveSelectedTank($request);
        if (!$tank) {
            return redirect()->route('dashboard')->withErrors([
                'tank' => 'Select or add a tank before applying species.',
            ]);
        }

        $selectedSpecies = Species::whereIn('id', $data['species_ids'])->get();
        $result = $this->calculateSharedPhRange($selectedSpecies);
        if (!$result['compatible']) {
            return redirect()->route('community.index')->withErrors([
                'species_ids' => 'Selected species do not share a common pH range.',
            ]);
        }

        $tank->species()->sync($selectedSpecies->pluck('id')->all());

        return redirect()->route('species.index')->with('success', 'Compatible species applied to current tank.');
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
            'message' => $min <= $max
                ? 'Compatible'
                : 'Selected species do not share a common pH range.',
        ];
    }
}
