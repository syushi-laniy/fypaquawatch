<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutomationRule;
use Illuminate\Http\Request;

class AutomationRuleController extends Controller
{
    public function index(Request $request)
    {
        $query = AutomationRule::query();
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('trigger', 'like', '%' . $search . '%')
                    ->orWhere('action', 'like', '%' . $search . '%');
            });
        }

        $rules = $query->orderBy('name')->get();

        return view('admin.automation-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.automation-rules.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'trigger' => 'required|string|max:255',
            'action' => 'required|string|max:255',
        ]);

        AutomationRule::create($data);

        return redirect()->route('automation-rules.index')->with('success', 'Automation rule created.');
    }

    public function edit(AutomationRule $automation_rule)
    {
        return view('admin.automation-rules.edit', ['rule' => $automation_rule]);
    }

    public function update(Request $request, AutomationRule $automation_rule)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'trigger' => 'required|string|max:255',
            'action' => 'required|string|max:255',
        ]);

        $automation_rule->update($data);

        return redirect()->route('automation-rules.index')->with('success', 'Automation rule updated.');
    }

    public function destroy(AutomationRule $automation_rule)
    {
        $automation_rule->delete();

        return redirect()->route('automation-rules.index')->with('success', 'Automation rule deleted.');
    }
}
