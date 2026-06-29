<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceApproval;
use App\Models\ConvenienceRequest;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\SalaryAdvance;
use App\Models\Loan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ApprovalManagementController extends Controller
{
    public function index()
    {
        $attendanceApprovals = AttendanceApproval::with('user')
            ->where('status', 'pending')
            ->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $manualAttendances = Attendance::with(['user.department'])
            ->where('via', '2')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $manualPendingApprovalKeys = AttendanceApproval::where('status', 'pending')
            ->get(['user_id', 'attendance_date'])
            ->mapWithKeys(function ($item) {
                return [$item->user_id . '_' . Carbon::parse($item->attendance_date)->format('Y-m-d') => true];
            })
            ->toArray();

        $leaveApprovals = Leave::with(['user.department', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $conveniencePending = ConvenienceRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $overtimePending = Overtime::with('user')
            ->where('status', 'pending')
            ->orderBy('overtime_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $salaryAdvancePending = SalaryAdvance::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $loanPending = Loan::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $pendingCounts = [
            'attendance' => $attendanceApprovals->count(),
            'leave'      => $leaveApprovals->count(),
            'convenience' => $conveniencePending->count(),
            'overtime'   => $overtimePending->count(),
            'advance'    => $salaryAdvancePending->count(),
            'loan'       => $loanPending->count(),
        ];

        return view('admin.approval-management.index', compact(
            'attendanceApprovals',
            'manualAttendances',
            'manualPendingApprovalKeys',
            'leaveApprovals',
            'conveniencePending',
            'overtimePending',
            'salaryAdvancePending',
            'loanPending',
            'pendingCounts'
        ));
    }

    public function completed()
    {
        $attendanceCompleted = AttendanceApproval::with('user')
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $leaveCompleted = Leave::with(['user.department', 'leaveType'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $convenienceComplete = ConvenienceRequest::with('user')
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $overtimeCompleted = Overtime::with('user')
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('overtime_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $salaryAdvanceCompleted = SalaryAdvance::with('user')
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        $loanCompleted = Loan::with('user')
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        return view('admin.approval-management.completed', compact(
            'attendanceCompleted',
            'leaveCompleted',
            'convenienceComplete',
            'overtimeCompleted',
            'salaryAdvanceCompleted',
            'loanCompleted'
        ));
    }

    public function updateAttendanceRequest(Request $request, int $id)
    {
        $request->validate([
            'requested_status' => 'required|string|max:50',
            'in_time'          => 'nullable|date_format:H:i',
            'out_time'         => 'nullable|date_format:H:i',
            'reason'           => 'nullable|string|max:1000',
        ]);

        $approval = AttendanceApproval::findOrFail($id);

        if ($approval->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be edited.');
        }

        $approval->requested_status = $request->requested_status;
        $approval->reason           = $request->reason;
        $approval->in_time          = $request->in_time
            ? Carbon::parse($approval->attendance_date->format('Y-m-d') . ' ' . $request->in_time)
            : null;
        $approval->out_time         = $request->out_time
            ? Carbon::parse($approval->attendance_date->format('Y-m-d') . ' ' . $request->out_time)
            : null;
        $approval->save();

        return back()->with('success', 'Attendance edit request updated successfully.');
    }

    public function sendManualAttendanceApproval(int $id)
    {
        $attendance = Attendance::where('via', '2')->findOrFail($id);

        $exists = AttendanceApproval::where('user_id', $attendance->user_id)
            ->whereDate('attendance_date', $attendance->date)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'A pending approval already exists for this employee and date.');
        }

        AttendanceApproval::create([
            'user_id'          => $attendance->user_id,
            'attendance_date'  => $attendance->date,
            'in_time'          => $attendance->in_time,
            'out_time'         => $attendance->out_time,
            'original_status'  => $attendance->status,
            'requested_status' => $attendance->status ?: 'Present',
            'reason'           => 'Manual attendance approval request' . ($attendance->remarks ? ': ' . $attendance->remarks : ''),
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Manual attendance sent for approval.');
    }

    public function updateLeaveApproval(Request $request, int $id)
    {
        $request->validate([
            'status'           => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string|max:1000',
        ]);

        $leave = Leave::findOrFail($id);
        $leave->status = $request->status;

        if ($request->status === 'approved') {
            $leave->approved_by      = auth()->id();
            $leave->rejection_reason = null;

            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
            foreach ($period as $date) {
                Attendance::updateOrCreate(
                    ['user_id' => $leave->user_id, 'date' => $date->format('Y-m-d')],
                    ['status'  => 'Leave']
                );
            }
        }

        if ($request->status === 'rejected') {
            $leave->rejection_reason = $request->rejection_reason;
        }

        $leave->save();

        return back()->with('success', 'Leave approval updated successfully.');
    }

    public function updateOvertimeApproval(Request $request, int $id)
    {
        $request->validate([
            'status'           => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string|max:1000',
        ]);

        $overtime = Overtime::findOrFail($id);

        if ($overtime->status !== 'pending') {
            return back()->with('error', 'Only pending overtime requests can be actioned.');
        }

        $overtime->status = $request->status;

        if ($request->status === 'approved') {
            $overtime->approved_by       = auth()->id();
            $overtime->rejection_reason  = null;
        } else {
            $overtime->rejection_reason  = $request->rejection_reason;
        }

        $overtime->save();

        return back()->with('success', 'Overtime request ' . $request->status . ' successfully.');
    }

    public function updateSalaryAdvanceApproval(Request $request, int $id)
    {
        $request->validate([
            'status'          => 'required|in:approved,rejected',
            'approved_amount' => 'nullable|numeric|min:0',
            'admin_remark'    => 'nullable|required_if:status,rejected|string|max:1000',
        ]);

        $advance = SalaryAdvance::findOrFail($id);

        if ($advance->status !== 'pending') {
            return back()->with('error', 'Only pending salary advance requests can be actioned.');
        }

        $advance->status       = $request->status;
        $advance->admin_remark = $request->admin_remark;

        if ($request->status === 'approved') {
            $advance->approved_by     = auth()->id();
            $advance->approved_amount = $request->approved_amount ?? $advance->requested_amount;
            $advance->approved_at     = now();
        }

        $advance->save();

        return back()->with('success', 'Salary advance request ' . $request->status . ' successfully.');
    }

    public function updateLoanApproval(Request $request, int $id)
    {
        $request->validate([
            'status'       => 'required|in:approved,rejected',
            'admin_remark' => 'nullable|required_if:status,rejected|string|max:1000',
        ]);

        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loan requests can be actioned.');
        }

        $loan->status       = $request->status;
        $loan->admin_remark = $request->admin_remark;

        if ($request->status === 'approved') {
            $loan->approved_by = auth()->id();
            $loan->approved_at = now();
        }

        $loan->save();

        return back()->with('success', 'Loan request ' . $request->status . ' successfully.');
    }
}
