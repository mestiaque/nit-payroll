<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceApproval;
use App\Models\ConvenienceRequest;
use App\Models\Leave;
use App\Models\Overtime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ApprovalManagementController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('type', 'all');

        $counts = [
            'attendance' => AttendanceApproval::where('status', 'pending')->count(),
            'leave' => Leave::where('status', 'pending')->count(),
            'conveyance' => ConvenienceRequest::where('status', 'pending')->count(),
            'overtime' => Overtime::where('status', 'pending')->count(),
        ];
        $counts['all'] = array_sum($counts);

        $items = $this->buildPendingItems($filter);

        return view('admin.approval-management.index', compact('items', 'filter', 'counts'));
    }

    public function completed(Request $request)
    {
        $filter = $request->get('type', 'all');

        $items = collect();

        if ($filter === 'all' || $filter === 'attendance') {
            AttendanceApproval::with('user')
                ->whereIn('status', ['approved', 'rejected'])
                ->orderByDesc('updated_at')
                ->limit(100)
                ->get()
                ->each(function ($row) use ($items) {
                    $items->push($this->formatItem('attendance', $row));
                });
        }

        if ($filter === 'all' || $filter === 'leave') {
            Leave::with(['user', 'leaveType'])
                ->whereIn('status', ['approved', 'rejected'])
                ->orderByDesc('updated_at')
                ->limit(100)
                ->get()
                ->each(function ($row) use ($items) {
                    $items->push($this->formatItem('leave', $row));
                });
        }

        if ($filter === 'all' || $filter === 'conveyance') {
            ConvenienceRequest::with('user')
                ->whereIn('status', ['approved', 'rejected'])
                ->orderByDesc('updated_at')
                ->limit(100)
                ->get()
                ->each(function ($row) use ($items) {
                    $items->push($this->formatItem('conveyance', $row));
                });
        }

        if ($filter === 'all' || $filter === 'overtime') {
            Overtime::with('user')
                ->whereIn('status', ['approved', 'rejected'])
                ->orderByDesc('updated_at')
                ->limit(100)
                ->get()
                ->each(function ($row) use ($items) {
                    $items->push($this->formatItem('overtime', $row));
                });
        }

        $items = $items->sortByDesc('sort_at')->values();

        return view('admin.approval-management.completed', compact('items', 'filter'));
    }

    public function approve(Request $request, string $type, int $id)
    {
        return $this->resolve($request, $type, $id, 'approved');
    }

    public function reject(Request $request, string $type, int $id)
    {
        $request->validate([
            'remark' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        return $this->resolve($request, $type, $id, 'rejected');
    }

    protected function resolve(Request $request, string $type, int $id, string $decision)
    {
        return match ($type) {
            'attendance' => $this->resolveAttendance($request, $id, $decision),
            'leave' => $this->resolveLeave($request, $id, $decision),
            'conveyance' => $this->resolveConveyance($request, $id, $decision),
            'overtime' => $this->resolveOvertime($request, $id, $decision),
            default => back()->with('error', 'Unknown approval type.'),
        };
    }

    protected function resolveAttendance(Request $request, int $id, string $decision)
    {
        $approval = AttendanceApproval::findOrFail($id);
        if ($approval->status !== 'pending') {
            return back()->with('error', 'This request is already processed.');
        }

        if ($decision === 'approved') {
            $status = strtolower($approval->requested_status ?? 'present');
            $attendance = Attendance::firstOrNew([
                'user_id' => $approval->user_id,
                'date' => Carbon::parse($approval->attendance_date)->format('Y-m-d'),
            ]);

            $attendance->fill([
                'status' => $status,
                'in_time' => $approval->in_time,
                'out_time' => $approval->out_time,
                'via' => '2',
                'device_sn' => 'Manual',
                'verify_type' => 'Manual_Entry',
                'remarks' => $approval->reason,
            ]);
            $attendance->save();
        }

        $approval->update([
            'status' => $decision,
            'admin_remark' => $request->remark ?? $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Attendance request ' . $decision . '.');
    }

    protected function resolveLeave(Request $request, int $id, string $decision)
    {
        $leave = Leave::findOrFail($id);
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Leave already processed.');
        }

        if ($decision === 'approved') {
            $leave->approved_by = auth()->id();
            $leave->rejection_reason = null;
            foreach (CarbonPeriod::create($leave->start_date, $leave->end_date) as $date) {
                Attendance::updateOrCreate(
                    ['user_id' => $leave->user_id, 'date' => $date->format('Y-m-d')],
                    ['status' => 'leave']
                );
            }
        } else {
            $leave->rejection_reason = $request->rejection_reason ?? $request->remark ?? 'Rejected';
        }

        $leave->status = $decision;
        $leave->save();

        return back()->with('success', 'Leave ' . $decision . '.');
    }

    protected function resolveConveyance(Request $request, int $id, string $decision)
    {
        $row = ConvenienceRequest::findOrFail($id);
        if ($row->status !== 'pending') {
            return back()->with('error', 'Conveyance request already processed.');
        }

        $row->update([
            'status' => $decision,
            'admin_remark' => $request->remark ?? $request->rejection_reason,
            'payment_status' => $decision === 'approved' ? 'unpaid' : $row->payment_status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Conveyance request ' . $decision . '.');
    }

    protected function resolveOvertime(Request $request, int $id, string $decision)
    {
        $row = Overtime::findOrFail($id);
        if ($row->status !== 'pending') {
            return back()->with('error', 'Overtime already processed.');
        }

        $payload = [
            'status' => $decision,
            'approved_by' => auth()->id(),
        ];
        if ($decision === 'rejected') {
            $payload['rejection_reason'] = $request->remark ?? $request->rejection_reason ?? 'Rejected';
        }
        $row->update($payload);

        return back()->with('success', 'Overtime ' . $decision . '.');
    }

    public function updateAttendanceRequest(Request $request, $id)
    {
        $request->validate([
            'requested_status' => 'required|string|max:50',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
            'reason' => 'nullable|string|max:1000',
        ]);

        $approval = AttendanceApproval::findOrFail($id);

        if ($approval->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be edited.');
        }

        $approval->requested_status = $request->requested_status;
        $approval->reason = $request->reason;
        $approval->in_time = $request->in_time
            ? Carbon::parse($approval->attendance_date->format('Y-m-d') . ' ' . $request->in_time)
            : null;
        $approval->out_time = $request->out_time
            ? Carbon::parse($approval->attendance_date->format('Y-m-d') . ' ' . $request->out_time)
            : null;
        $approval->save();

        return back()->with('success', 'Attendance request updated.');
    }

    protected function buildPendingItems(string $filter)
    {
        $items = collect();

        if ($filter === 'all' || $filter === 'attendance') {
            AttendanceApproval::with('user')
                ->where('status', 'pending')
                ->orderByDesc('attendance_date')
                ->limit(200)
                ->get()
                ->each(fn ($row) => $items->push($this->formatItem('attendance', $row)));
        }

        if ($filter === 'all' || $filter === 'leave') {
            Leave::with(['user', 'leaveType'])
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->limit(200)
                ->get()
                ->each(fn ($row) => $items->push($this->formatItem('leave', $row)));
        }

        if ($filter === 'all' || $filter === 'conveyance') {
            ConvenienceRequest::with('user')
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->limit(200)
                ->get()
                ->each(fn ($row) => $items->push($this->formatItem('conveyance', $row)));
        }

        if ($filter === 'all' || $filter === 'overtime') {
            Overtime::with('user')
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->limit(200)
                ->get()
                ->each(fn ($row) => $items->push($this->formatItem('overtime', $row)));
        }

        return $items->sortByDesc('sort_at')->values();
    }

    protected function formatItem(string $type, $row): array
    {
        return match ($type) {
            'attendance' => [
                'type' => 'attendance',
                'type_label' => 'Attendance',
                'id' => $row->id,
                'employee' => $row->user->name ?? 'N/A',
                'summary' => ($row->original_status ?? '-') . ' → ' . ($row->requested_status ?? '-'),
                'detail' => ($row->attendance_date ? $row->attendance_date->format('d M Y') : '')
                    . ' | In: ' . ($row->in_time ? $row->in_time->format('H:i') : '-')
                    . ' Out: ' . ($row->out_time ? $row->out_time->format('H:i') : '-'),
                'reason' => $row->reason,
                'status' => $row->status,
                'sort_at' => $row->attendance_date ?? $row->created_at,
                'meta' => $row,
            ],
            'leave' => [
                'type' => 'leave',
                'type_label' => 'Leave',
                'id' => $row->id,
                'employee' => $row->user->name ?? 'N/A',
                'summary' => ($row->leaveType->name ?? 'Leave') . ' (' . ($row->days ?? '-') . ' days)',
                'detail' => ($row->start_date?->format('d M Y') ?? '-') . ' – ' . ($row->end_date?->format('d M Y') ?? '-'),
                'reason' => $row->reason,
                'status' => $row->status,
                'sort_at' => $row->created_at,
                'meta' => $row,
            ],
            'conveyance' => [
                'type' => 'conveyance',
                'type_label' => 'Conveyance',
                'id' => $row->id,
                'employee' => $row->user->name ?? 'N/A',
                'summary' => ucfirst(str_replace('_', ' ', $row->type)) . ' — ৳' . number_format($row->amount, 2),
                'detail' => $row->created_at?->format('d M Y H:i') ?? '-',
                'reason' => $row->reason,
                'status' => $row->status,
                'sort_at' => $row->created_at,
                'meta' => $row,
            ],
            'overtime' => [
                'type' => 'overtime',
                'type_label' => 'Overtime',
                'id' => $row->id,
                'employee' => $row->user->name ?? 'N/A',
                'summary' => ($row->hours ?? 0) . ' hrs — ৳' . number_format($row->amount ?? 0, 2),
                'detail' => ($row->month ?? '-') . '/' . ($row->year ?? '-'),
                'reason' => $row->reason ?? '-',
                'status' => $row->status,
                'sort_at' => $row->created_at,
                'meta' => $row,
            ],
            default => [],
        };
    }
}
