<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiAnalysisLog;
use Illuminate\Http\Request;

class FishAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $query = AiAnalysisLog::with(['user', 'tank']);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('disease_name', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('tank', function ($tankQuery) use ($search) {
                        $tankQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $analyses = $query->latest()->paginate(20)->withQueryString();

        return view('admin.fish-analyses.index', compact('analyses'));
    }
}
