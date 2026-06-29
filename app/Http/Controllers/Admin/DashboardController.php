<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiAnalysisLog;
use App\Models\AutomationRule;
use App\Models\Parameter;
use App\Models\Tank;
use App\Models\TankReading;
use App\Models\Threshold;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'tanks' => Tank::count(),
            'parameters' => Parameter::count(),
            'thresholds' => Threshold::count(),
            'rules' => AutomationRule::count(),
            'users' => User::where('role', 'user')->count(),
            'analyses' => AiAnalysisLog::count(),
        ];

        $recentAnalyses = AiAnalysisLog::with(['user', 'tank'])
            ->latest()
            ->limit(8)
            ->get();

        $thresholds = Threshold::all()->keyBy('parameter');
        $recentReadings = TankReading::with('tank')
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(function (TankReading $reading) use ($thresholds) {
                $threshold = $thresholds->get($reading->parameter);
                $value = is_numeric($reading->value) ? (float) $reading->value : null;
                $isGood = $value !== null
                    && $threshold
                    && is_numeric($threshold->min_value)
                    && is_numeric($threshold->max_value)
                    && $value >= (float) $threshold->min_value
                    && $value <= (float) $threshold->max_value;

                return [
                    'date' => $reading->recorded_at ?? $reading->created_at,
                    'tank' => $reading->tank,
                    'parameter' => $reading->parameter,
                    'value' => $reading->value,
                    'unit' => $reading->unit,
                    'status' => $isGood ? 'Good' : 'Warning',
                ];
            });

        return view('admin.dashboard', compact('stats', 'recentAnalyses', 'recentReadings'));
    }
}
