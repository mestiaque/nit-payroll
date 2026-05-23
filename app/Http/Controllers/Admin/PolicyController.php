<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /**
     * Validate and normalize policy payload before save.
     */
    private function validateAndNormalize(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'value' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $data = [
            'name' => $request->name,
            'type' => $request->type,
            'value' => (float) $request->value,
            'unit' => $request->unit,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
        ];

        // Make rule explicit and simple: 3 late = 1 absent day deduction.
        if ($data['type'] === 'late_count_for_absent') {
            $data['value'] = max(1, (int) round($data['value']));
            $data['unit'] = 'count';
        }

        if ($data['type'] === 'absent_count_for_deduction') {
            $data['value'] = max(1, (int) round($data['value']));
            $data['unit'] = 'count';
        }

        // Percentage policies should stay within a sensible range.
        if (in_array($data['type'], ['absent_deduction_percentage', 'provident_fund_percentage'], true)) {
            $data['value'] = min(100, max(0, $data['value']));
            $data['unit'] = 'percentage';
        }

        if (in_array($data['type'], ['grace_time_minutes', 'late_threshold_minutes'], true)) {
            $data['value'] = max(0, (int) round($data['value']));
            $data['unit'] = 'minutes';
        }

        if ($data['type'] === 'working_hours_per_day') {
            $data['value'] = max(0, $data['value']);
            $data['unit'] = 'hours';
        }

        return $data;
    }

    /**
     * Display a listing of policies.
     */
    public function index()
    {
        $policies = Policy::orderBy('type')->get();
        $policies    = Policy::orderBy('type')->get();
        return view('admin.policy.index', compact('policies'));
    }

    /**
     * Show the form for creating a new policy.
     */
    public function create()
    {
        return view('admin.policy.create');
    }

    /**
     * Store a newly created policy.
     */
    public function store(Request $request)
    {
        $data = $this->validateAndNormalize($request);
        Policy::create($data);

        return redirect()->route('admin.policy.index')->with('success', 'Policy added successfully');
    }

    /**
     * Update the policy.
     */
    public function update(Request $request, $id)
    {
        $policy = Policy::findOrFail($id);
        $data = $this->validateAndNormalize($request);
        $policy->update($data);
        return redirect()->route('admin.policy.index')->with('success', 'Policy updated successfully');
    }

    /**
     * Remove the policy.
     */
    public function destroy($id)
    {
        Policy::findOrFail($id)->delete();
        return redirect()->route('admin.policy.index')->with('success', 'Policy deleted successfully');
    }
}
