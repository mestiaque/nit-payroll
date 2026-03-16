@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Approval Management') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card mb-4 mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Approval Management - Pending</h5>
            <a href="{{ route('admin.approvals.completed') }}" class="btn btn-sm btn-dark">
                <i class="fa fa-list"></i> View Approved / Rejected List
            </a>
        </div>
        <div class="card-body">
            <p class="mb-0">Only pending approvals are shown here.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Attendance Edit Approval</h5>
            <a href="{{ route('admin.attendance-approval.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> New Request</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Original</th>
                            <th>Requested</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceApprovals as $approval)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $approval->user->name ?? 'N/A' }}</td>
                                <td>{{ $approval->attendance_date ? $approval->attendance_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $approval->original_status ?? 'N/A' }}</td>
                                <td>{{ $approval->requested_status }}</td>
                                <td>{{ $approval->in_time ? $approval->in_time->format('H:i') : '-' }}</td>
                                <td>{{ $approval->out_time ? $approval->out_time->format('H:i') : '-' }}</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <form action="{{ route('admin.attendance-approval.update', $approval->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <details class="d-inline-block align-middle ms-1">
                                        <summary class="btn btn-sm btn-danger" style="display:inline-block; cursor:pointer; list-style:none;">Reject</summary>
                                        <form action="{{ route('admin.attendance-approval.update', $approval->id) }}" method="POST" class="mt-2" style="min-width:260px;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <textarea name="admin_remark" class="form-control form-control-sm mb-1" rows="2" placeholder="Reject note" required></textarea>
                                            <button type="submit" class="btn btn-sm btn-danger">Submit Rejection</button>
                                        </form>
                                    </details>
                                    <details class="mt-2">
                                        <summary>Edit Request</summary>
                                        <form action="{{ route('admin.approvals.attendance.edit', $approval->id) }}" method="POST" class="row g-2 mt-1">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-3">
                                                <select name="requested_status" class="form-control form-control-sm" required>
                                                    <option value="present" {{ $approval->requested_status == 'present' ? 'selected' : '' }}>Present</option>
                                                    <option value="late" {{ $approval->requested_status == 'late' ? 'selected' : '' }}>Late</option>
                                                    <option value="absent" {{ $approval->requested_status == 'absent' ? 'selected' : '' }}>Absent</option>
                                                    <option value="Leave" {{ $approval->requested_status == 'Leave' ? 'selected' : '' }}>Leave</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="time" name="in_time" class="form-control form-control-sm" value="{{ $approval->in_time ? $approval->in_time->format('H:i') : '' }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="time" name="out_time" class="form-control form-control-sm" value="{{ $approval->out_time ? $approval->out_time->format('H:i') : '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="reason" class="form-control form-control-sm" value="{{ $approval->reason }}" placeholder="Reason">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-sm btn-primary w-100">Update</button>
                                            </div>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No pending attendance edit request found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Manual Attendance Approval</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($manualAttendances as $attendance)
                            @php
                                $approvalKey = $attendance->user_id . '_' . \Carbon\Carbon::parse($attendance->date)->format('Y-m-d');
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attendance->user->name ?? 'N/A' }}</td>
                                <td>{{ $attendance->date }}</td>
                                <td>{{ $attendance->in_time ? $attendance->in_time->format('H:i') : '-' }}</td>
                                <td>{{ $attendance->out_time ? $attendance->out_time->format('H:i') : '-' }}</td>
                                <td>{{ $attendance->remarks ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.attendance.manual.edit', $attendance->id) }}" class="btn btn-sm btn-info">Edit</a>
                                    @if(isset($manualPendingApprovalKeys[$approvalKey]))
                                        <span class="badge badge-warning">Pending Approval</span>
                                    @else
                                        <form action="{{ route('admin.approvals.manual.send', $attendance->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Send for Approval</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No manual attendance data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Leave Approval</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveApprovals as $leave)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $leave->user->name ?? 'N/A' }}</td>
                                <td>{{ $leave->leaveType->name ?? 'N/A' }}</td>
                                <td>{{ $leave->start_date ? $leave->start_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $leave->end_date ? $leave->end_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $leave->days }}</td>
                                <td>{{ $leave->reason ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.approvals.leave.update', $leave->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <details class="d-inline-block align-middle ms-1">
                                        <summary class="btn btn-sm btn-danger" style="display:inline-block; cursor:pointer; list-style:none;">Reject</summary>
                                        <form action="{{ route('admin.approvals.leave.update', $leave->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <textarea name="rejection_reason" class="form-control form-control-sm mb-1" rows="2" placeholder="Reject note" required></textarea>
                                            <button type="submit" class="btn btn-sm btn-danger">Submit Rejection</button>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No pending leave approval found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Convenience Approval</h5>
            <a href="{{ route('admin.convenience.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add New</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conveniencePending as $request)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $request->user->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($request->type) }}</td>
                                <td>{{ number_format($request->amount, 2) }}</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>{{ $request->created_at ? $request->created_at->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    <form action="{{ route('admin.convenience.update', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <details class="d-inline-block align-middle ms-1">
                                        <summary class="btn btn-sm btn-danger" style="display:inline-block; cursor:pointer; list-style:none;">Reject</summary>
                                        <form action="{{ route('admin.convenience.update', $request->id) }}" method="POST" class="mt-2" style="min-width:260px;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <textarea name="admin_remark" class="form-control form-control-sm mb-1" rows="2" placeholder="Reject note" required></textarea>
                                            <button type="submit" class="btn btn-sm btn-danger">Submit Rejection</button>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No pending convenience request found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
