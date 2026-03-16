<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Performance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /**
     * Display a listing of performances.
     */
    public function index(Request $request)
    {
        $performances = Performance::with('user', 'reviewer')
            ->when($request->year, function($q) use ($request) {
                $q->where('year', $request->year);
            })
            ->when($request->report_month, function($q) use ($request) {
                $q->where('report_month', $request->report_month);
            })
            ->orderBy('year', 'desc')
            ->orderBy('report_month', 'desc')
            ->get();

        $years = range(date('Y'), date('Y') - 5);

        return view('admin.performance.index', compact('performances', 'years'));
    }

    /**
     * Show the form for creating a new performance review.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterBy('employee')->get();
        $years = range(date('Y'), date('Y') - 5);
        return view('admin.performance.create', compact('users', 'years'));
    }

    /**
     * Store a newly created performance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required',
            'report_month' => 'required|integer|min:1|max:12',
            'rating' => 'required|numeric|min:0|max:5',
            'dress_score' => 'nullable|numeric|min:0|max:5',
            'behavior_score' => 'nullable|numeric|min:0|max:5',
            'dress_note' => 'nullable|string|max:1000',
            'behavior_note' => 'nullable|string|max:1000',
        ]);

        $report = $this->buildReportStats((int) $request->user_id, (int) $request->year, (int) $request->report_month);

        Performance::create([
            'user_id' => $request->user_id,
            'reviewer_id' => auth()->id(),
            'year' => $request->year,
            'quarter' => 'annual',
            'report_month' => $request->report_month,
            'rating' => $request->rating,
            'attendance_score' => $request->attendance_score ?? 0,
            'task_completion' => $request->task_completion ?? 0,
            'teamwork' => $request->teamwork ?? 0,
            'initiative' => $request->initiative ?? 0,
            'punctuality' => $request->punctuality ?? 0,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'comments' => $request->comments,
            'goals' => $request->goals,
            'report_start_date' => $report['start_date'],
            'report_end_date' => $report['end_date'],
            'present_days' => $report['present_days'],
            'late_days' => $report['late_days'],
            'absent_days' => $report['absent_days'],
            'leave_days' => $report['leave_days'],
            'approved_leave_requests' => $report['approved_leave_requests'],
            'dress_score' => $request->dress_score ?? 0,
            'behavior_score' => $request->behavior_score ?? 0,
            'dress_note' => $request->dress_note,
            'behavior_note' => $request->behavior_note,
            'status' => 'reviewed',
        ]);

        return redirect()->route('admin.performance.index')->with('success', 'Performance review added successfully');
    }

    /**
     * Update the performance.
     */
    public function update(Request $request, $id)
    {
        $performance = Performance::findOrFail($id);

        $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
            'report_month' => 'required|integer|min:1|max:12',
            'dress_score' => 'nullable|numeric|min:0|max:5',
            'behavior_score' => 'nullable|numeric|min:0|max:5',
            'dress_note' => 'nullable|string|max:1000',
            'behavior_note' => 'nullable|string|max:1000',
        ]);

        $reportMonth = (int) $request->report_month;
        $report = $this->buildReportStats((int) $performance->user_id, (int) $performance->year, $reportMonth);

        $performance->update([
            'rating' => $request->rating,
            'attendance_score' => $request->attendance_score ?? 0,
            'task_completion' => $request->task_completion ?? 0,
            'teamwork' => $request->teamwork ?? 0,
            'initiative' => $request->initiative ?? 0,
            'punctuality' => $request->punctuality ?? 0,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'comments' => $request->comments,
            'goals' => $request->goals,
            'report_month' => $reportMonth,
            'report_start_date' => $report['start_date'],
            'report_end_date' => $report['end_date'],
            'present_days' => $report['present_days'],
            'late_days' => $report['late_days'],
            'absent_days' => $report['absent_days'],
            'leave_days' => $report['leave_days'],
            'approved_leave_requests' => $report['approved_leave_requests'],
            'dress_score' => $request->dress_score ?? 0,
            'behavior_score' => $request->behavior_score ?? 0,
            'dress_note' => $request->dress_note,
            'behavior_note' => $request->behavior_note,
        ]);

        return redirect()->route('admin.performance.index')->with('success', 'Performance updated successfully');
    }

    /**
     * Remove the performance.
     */
    public function destroy($id)
    {
        Performance::findOrFail($id)->delete();
        return redirect()->route('admin.performance.index')->with('success', 'Performance deleted successfully');
    }

    /**
     * Return report-style attendance and leave summary for selected period.
     */
    public function reportData(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required|integer',
            'report_month' => 'required|integer|min:1|max:12',
        ]);

        return response()->json($this->buildReportStats(
            (int) $request->user_id,
            (int) $request->year,
            (int) $request->report_month
        ));
    }

    private function buildReportStats(int $userId, int $year, int $month): array
    {
        [$startDate, $endDate] = $this->resolveMonthRange($year, $month);

        $attendanceBase = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);

        $presentDays = (clone $attendanceBase)->whereIn('status', ['present', 'Present', 'P'])->count();
        $lateDays = (clone $attendanceBase)->whereIn('status', ['late', 'Late', 'LT'])->count();
        $absentDays = (clone $attendanceBase)->whereIn('status', ['absent', 'Absent', 'A'])->count();
        $attendanceLeaveDays = (clone $attendanceBase)->whereIn('status', ['leave', 'Leave', 'L'])->count();

        $approvedLeaveQuery = Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $endDate->toDateString())
            ->whereDate('end_date', '>=', $startDate->toDateString());

        $approvedLeaveRequests = (clone $approvedLeaveQuery)->count();

        $approvedLeaveDays = (clone $approvedLeaveQuery)->get()->sum(function ($leave) use ($startDate, $endDate) {
            $overlapStart = Carbon::parse($leave->start_date)->max($startDate->copy()->startOfDay());
            $overlapEnd = Carbon::parse($leave->end_date)->min($endDate->copy()->endOfDay());

            if ($overlapEnd->lt($overlapStart)) {
                return 0;
            }

            return $overlapStart->diffInDays($overlapEnd) + 1;
        });

        $leaveDays = max($attendanceLeaveDays, (int) $approvedLeaveDays);

        return [
            'report_month' => $month,
            'report_month_name' => Carbon::create($year, $month, 1)->format('F'),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'approved_leave_requests' => $approvedLeaveRequests,
        ];
    }

    private function resolveMonthRange(int $year, int $month): array
    {
        $date = Carbon::create($year, $month, 1);
        return [$date->copy()->startOfMonth()->startOfDay(), $date->copy()->endOfMonth()->endOfDay()];
    }
}
