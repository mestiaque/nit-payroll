@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Approval Management') }}</title>
@endsection

@push('css')
<style>
    .apr-summary { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:18px; }
    .apr-badge {
        display:flex; align-items:center; gap:8px; padding:10px 16px;
        border-radius:10px; background:#fff; border:1px solid #dde6f5;
        box-shadow:0 2px 8px rgba(15,23,42,.06); font-size:13px;
        cursor:pointer; transition:border-color .15s;
        text-decoration:none; color:inherit;
    }
    .apr-badge:hover { border-color:#1d72f3; }
    .apr-badge .apr-count {
        font-size:20px; font-weight:700; line-height:1;
    }
    .apr-badge .apr-label { color:#5f6f82; font-size:12px; }
    .apr-badge.pending .apr-count { color:#e67e22; }
    .apr-badge.zero .apr-count { color:#27ae60; }

    .nav-tabs .nav-link { font-size:13px; font-weight:600; }
    .nav-tabs .nav-link .badge-count {
        display:inline-block; background:#dc3545; color:#fff;
        border-radius:999px; padding:0 6px; font-size:11px;
        margin-left:5px; line-height:18px;
    }
    .nav-tabs .nav-link .badge-count.zero { background:#6c757d; }

    .table-sm td, .table-sm th { vertical-align:middle; }
    .approve-form { display:inline-block; }

    details.action-details summary { list-style:none; cursor:pointer; }
    details.action-details summary::-webkit-details-marker { display:none; }
    details.action-details[open] summary { margin-bottom:6px; }
    .reject-panel {
        background:#fff5f5; border:1px solid #f5c6cb; border-radius:6px;
        padding:8px; margin-top:4px; min-width:260px;
    }
</style>
@endpush

@section('contents')
@include(adminTheme().'alerts')

<div class="breadcrumb-area">
    <h1>Approval Management</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Approval Management</li>
        <li class="item">Pending</li>
    </ol>
</div>

<div class="flex-grow-1">

    {{-- Summary Badges --}}
    <div class="apr-summary">
        <a href="#tab-attendance" class="apr-badge {{ $pendingCounts['attendance'] > 0 ? 'pending' : 'zero' }}" onclick="switchTab('tab-attendance')">
            <i class="bx bx-calendar-check" style="font-size:22px; color:#0d6efd;"></i>
            <div>
                <div class="apr-count">{{ $pendingCounts['attendance'] }}</div>
                <div class="apr-label">Attendance</div>
            </div>
        </a>
        <a href="#tab-leave" class="apr-badge {{ $pendingCounts['leave'] > 0 ? 'pending' : 'zero' }}" onclick="switchTab('tab-leave')">
            <i class="bx bx-time-five" style="font-size:22px; color:#6f42c1;"></i>
            <div>
                <div class="apr-count">{{ $pendingCounts['leave'] }}</div>
                <div class="apr-label">Leave</div>
            </div>
        </a>
        <a href="#tab-overtime" class="apr-badge {{ $pendingCounts['overtime'] > 0 ? 'pending' : 'zero' }}" onclick="switchTab('tab-overtime')">
            <i class="bx bx-run" style="font-size:22px; color:#e67e22;"></i>
            <div>
                <div class="apr-count">{{ $pendingCounts['overtime'] }}</div>
                <div class="apr-label">Overtime</div>
            </div>
        </a>
        <a href="#tab-convenience" class="apr-badge {{ $pendingCounts['convenience'] > 0 ? 'pending' : 'zero' }}" onclick="switchTab('tab-convenience')">
            <i class="bx bx-money" style="font-size:22px; color:#27ae60;"></i>
            <div>
                <div class="apr-count">{{ $pendingCounts['convenience'] }}</div>
                <div class="apr-label">Convenience</div>
            </div>
        </a>
        <a href="#tab-advance" class="apr-badge {{ $pendingCounts['advance'] > 0 ? 'pending' : 'zero' }}" onclick="switchTab('tab-advance')">
            <i class="bx bx-wallet" style="font-size:22px; color:#c0392b;"></i>
            <div>
                <div class="apr-count">{{ $pendingCounts['advance'] }}</div>
                <div class="apr-label">Salary Advance</div>
            </div>
        </a>
        <a href="#tab-loan" class="apr-badge {{ $pendingCounts['loan'] > 0 ? 'pending' : 'zero' }}" onclick="switchTab('tab-loan')">
            <i class="bx bx-building-house" style="font-size:22px; color:#2c3e50;"></i>
            <div>
                <div class="apr-count">{{ $pendingCounts['loan'] }}</div>
                <div class="apr-label">Loan</div>
            </div>
        </a>
        <a href="{{ route('admin.approvals.completed') }}" class="apr-badge" style="margin-left:auto; border-color:#6c757d;">
            <i class="bx bx-check-double" style="font-size:22px; color:#6c757d;"></i>
            <div>
                <div class="apr-count" style="color:#6c757d;">→</div>
                <div class="apr-label">View Completed</div>
            </div>
        </a>
    </div>

    {{-- Tabs --}}
    <div class="card">
        <div class="card-header p-0">
            <ul class="nav nav-tabs" id="approvalTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-attendance" id="tab-attendance-link">
                        Attendance
                        <span class="badge-count {{ $pendingCounts['attendance'] == 0 ? 'zero' : '' }}">{{ $pendingCounts['attendance'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-manual" id="tab-manual-link">
                        Manual Att.
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-leave" id="tab-leave-link">
                        Leave
                        <span class="badge-count {{ $pendingCounts['leave'] == 0 ? 'zero' : '' }}">{{ $pendingCounts['leave'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-overtime" id="tab-overtime-link">
                        Overtime
                        <span class="badge-count {{ $pendingCounts['overtime'] == 0 ? 'zero' : '' }}">{{ $pendingCounts['overtime'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-convenience" id="tab-convenience-link">
                        Convenience
                        <span class="badge-count {{ $pendingCounts['convenience'] == 0 ? 'zero' : '' }}">{{ $pendingCounts['convenience'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-advance" id="tab-advance-link">
                        Salary Advance
                        <span class="badge-count {{ $pendingCounts['advance'] == 0 ? 'zero' : '' }}">{{ $pendingCounts['advance'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-loan" id="tab-loan-link">
                        Loan
                        <span class="badge-count {{ $pendingCounts['loan'] == 0 ? 'zero' : '' }}">{{ $pendingCounts['loan'] }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body tab-content p-3">

            {{-- ATTENDANCE EDIT APPROVAL --}}
            <div class="tab-pane fade show active" id="tab-attendance">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Pending Attendance Edit Requests</h6>
                    <a href="{{ route('admin.attendance-approval.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-plus"></i> New Request</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th><th>Employee</th><th>Date</th>
                                <th>Original</th><th>Requested</th>
                                <th>In</th><th>Out</th><th>Reason</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($attendanceApprovals as $a)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $a->user->name ?? 'N/A' }}</td>
                                <td>{{ $a->attendance_date?->format('d-M-Y') ?? 'N/A' }}</td>
                                <td><span class="badge bg-secondary">{{ $a->original_status ?? 'N/A' }}</span></td>
                                <td><span class="badge bg-warning text-dark">{{ $a->requested_status }}</span></td>
                                <td>{{ $a->in_time ? $a->in_time->format('H:i') : '—' }}</td>
                                <td>{{ $a->out_time ? $a->out_time->format('H:i') : '—' }}</td>
                                <td>{{ Str::limit($a->reason, 40) }}</td>
                                <td>
                                    <form action="{{ route('admin.attendance-approval.update', $a->id) }}" method="POST" class="approve-form">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-success">✓ Approve</button>
                                    </form>
                                    <details class="action-details d-inline-block ms-1">
                                        <summary><span class="btn btn-sm btn-danger">✗ Reject</span></summary>
                                        <div class="reject-panel">
                                            <form action="{{ route('admin.attendance-approval.update', $a->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <textarea name="admin_remark" class="form-control form-control-sm mb-1" rows="2" placeholder="Rejection note" required></textarea>
                                                <button class="btn btn-sm btn-danger w-100">Submit Rejection</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted">No pending attendance edit requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MANUAL ATTENDANCE APPROVAL --}}
            <div class="tab-pane fade" id="tab-manual">
                <h6 class="mb-2">Manual Attendance Records</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Employee</th><th>Department</th><th>Date</th><th>In</th><th>Out</th><th>Remarks</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        @forelse($manualAttendances as $ma)
                            @php $key = $ma->user_id . '_' . \Carbon\Carbon::parse($ma->date)->format('Y-m-d'); @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ma->user->name ?? 'N/A' }}</td>
                                <td>{{ $ma->user->department?->name ?? '—' }}</td>
                                <td>{{ $ma->date }}</td>
                                <td>{{ $ma->in_time ? $ma->in_time->format('H:i') : '—' }}</td>
                                <td>{{ $ma->out_time ? $ma->out_time->format('H:i') : '—' }}</td>
                                <td>{{ $ma->remarks ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.attendance.manual.edit', $ma->id) }}" class="btn btn-sm btn-info">Edit</a>
                                    @if(isset($manualPendingApprovalKeys[$key]))
                                        <span class="badge bg-warning text-dark">Pending Approval</span>
                                    @else
                                        <form action="{{ route('admin.approvals.manual.send', $ma->id) }}" method="POST" class="approve-form">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Send for Approval</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No manual attendance records.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- LEAVE APPROVAL --}}
            <div class="tab-pane fade" id="tab-leave">
                <h6 class="mb-2">Pending Leave Requests</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Employee</th><th>Dept</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        @forelse($leaveApprovals as $lv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $lv->user->name ?? 'N/A' }}</td>
                                <td>{{ $lv->user->department?->name ?? '—' }}</td>
                                <td>{{ $lv->leaveType->name ?? 'N/A' }}</td>
                                <td>{{ $lv->start_date?->format('d-M-Y') }}</td>
                                <td>{{ $lv->end_date?->format('d-M-Y') }}</td>
                                <td><strong>{{ $lv->days }}</strong></td>
                                <td>{{ Str::limit($lv->reason, 40) }}</td>
                                <td>
                                    <form action="{{ route('admin.approvals.leave.update', $lv->id) }}" method="POST" class="approve-form">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-success">✓ Approve</button>
                                    </form>
                                    <details class="action-details d-inline-block ms-1">
                                        <summary><span class="btn btn-sm btn-danger">✗ Reject</span></summary>
                                        <div class="reject-panel">
                                            <form action="{{ route('admin.approvals.leave.update', $lv->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <textarea name="rejection_reason" class="form-control form-control-sm mb-1" rows="2" placeholder="Rejection note" required></textarea>
                                                <button class="btn btn-sm btn-danger w-100">Submit Rejection</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted">No pending leave requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- OVERTIME APPROVAL --}}
            <div class="tab-pane fade" id="tab-overtime">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Pending Overtime Requests <small class="text-muted">[Bangladesh Labour Act 2006, §108–109: max 2 hrs/day, double rate]</small></h6>
                    <a href="{{ route('admin.overtimes.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-plus"></i> Add Overtime</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Employee</th><th>Date</th><th>Type</th><th>Start</th><th>End</th><th>Hours</th><th>Rate</th><th>Amount</th><th>Reason</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        @forelse($overtimePending as $ot)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ot->user->name ?? 'N/A' }}</td>
                                <td>{{ $ot->overtime_date?->format('d-M-Y') }}</td>
                                <td>{{ $ot->overtime_type ?? '—' }}</td>
                                <td>{{ $ot->start_time ? $ot->start_time->format('H:i') : '—' }}</td>
                                <td>{{ $ot->end_time ? $ot->end_time->format('H:i') : '—' }}</td>
                                <td><strong>{{ number_format($ot->hours, 2) }}</strong></td>
                                <td>{{ number_format($ot->rate, 2) }}</td>
                                <td><strong class="text-primary">{{ number_format($ot->amount, 2) }}</strong></td>
                                <td>{{ Str::limit($ot->reason, 30) }}</td>
                                <td>
                                    <form action="{{ route('admin.approvals.overtime.update', $ot->id) }}" method="POST" class="approve-form">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-success">✓ Approve</button>
                                    </form>
                                    <details class="action-details d-inline-block ms-1">
                                        <summary><span class="btn btn-sm btn-danger">✗ Reject</span></summary>
                                        <div class="reject-panel">
                                            <form action="{{ route('admin.approvals.overtime.update', $ot->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <textarea name="rejection_reason" class="form-control form-control-sm mb-1" rows="2" placeholder="Rejection reason" required></textarea>
                                                <button class="btn btn-sm btn-danger w-100">Submit Rejection</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center text-muted">No pending overtime requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CONVENIENCE APPROVAL --}}
            <div class="tab-pane fade" id="tab-convenience">
                <h6 class="mb-2">Pending Convenience Requests</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Employee</th><th>Type</th><th>Amount</th><th>Reason</th><th>Date</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        @forelse($conveniencePending as $cv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $cv->user->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($cv->type) }}</td>
                                <td><strong>{{ number_format($cv->amount, 2) }}</strong></td>
                                <td>{{ Str::limit($cv->reason ?? '', 40) }}</td>
                                <td>{{ $cv->created_at?->format('d-M-Y') }}</td>
                                <td>
                                    <details class="action-details d-inline-block">
                                        <summary><span class="btn btn-sm btn-success">✓ Approve</span></summary>
                                        <div class="reject-panel" style="background:#f0fff4; border-color:#c3e6cb;">
                                            <form action="{{ route('admin.convenience.update', $cv->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <textarea name="admin_remark" class="form-control form-control-sm mb-1" rows="2" placeholder="Approval note (optional)"></textarea>
                                                <button class="btn btn-sm btn-success w-100">Submit Approval</button>
                                            </form>
                                        </div>
                                    </details>
                                    <details class="action-details d-inline-block ms-1">
                                        <summary><span class="btn btn-sm btn-danger">✗ Reject</span></summary>
                                        <div class="reject-panel">
                                            <form action="{{ route('admin.convenience.update', $cv->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <textarea name="admin_remark" class="form-control form-control-sm mb-1" rows="2" placeholder="Rejection note" required></textarea>
                                                <button class="btn btn-sm btn-danger w-100">Submit Rejection</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No pending convenience requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- SALARY ADVANCE APPROVAL --}}
            <div class="tab-pane fade" id="tab-advance">
                <h6 class="mb-2">Pending Salary Advance Requests</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Employee</th><th>Requested Amt</th><th>Installments</th><th>Monthly Ded.</th><th>Date</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        @forelse($salaryAdvancePending as $sa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sa->user->name ?? 'N/A' }}</td>
                                <td><strong>{{ number_format($sa->requested_amount, 2) }}</strong></td>
                                <td>{{ $sa->installment_months ?? '—' }}</td>
                                <td>{{ number_format($sa->monthly_deduction ?? 0, 2) }}</td>
                                <td>{{ $sa->created_at?->format('d-M-Y') }}</td>
                                <td>
                                    <details class="action-details d-inline-block">
                                        <summary><span class="btn btn-sm btn-success">✓ Approve</span></summary>
                                        <div class="reject-panel" style="background:#f0fff4; border-color:#c3e6cb;">
                                            <form action="{{ route('admin.approvals.salary-advance.update', $sa->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <div class="mb-1">
                                                    <label class="form-label form-label-sm">Approved Amount</label>
                                                    <input type="number" step="0.01" name="approved_amount" class="form-control form-control-sm" value="{{ $sa->requested_amount }}" required>
                                                </div>
                                                <button class="btn btn-sm btn-success w-100">Submit Approval</button>
                                            </form>
                                        </div>
                                    </details>
                                    <details class="action-details d-inline-block ms-1">
                                        <summary><span class="btn btn-sm btn-danger">✗ Reject</span></summary>
                                        <div class="reject-panel">
                                            <form action="{{ route('admin.approvals.salary-advance.update', $sa->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <textarea name="admin_remark" class="form-control form-control-sm mb-1" rows="2" placeholder="Rejection reason" required></textarea>
                                                <button class="btn btn-sm btn-danger w-100">Submit Rejection</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No pending salary advance requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- LOAN APPROVAL --}}
            <div class="tab-pane fade" id="tab-loan">
                <h6 class="mb-2">Pending Loan Requests</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Employee</th><th>Principal</th><th>Interest %</th><th>Total Amt</th><th>Monthly Inst.</th><th>Disbursement Date</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        @forelse($loanPending as $ln)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ln->user->name ?? 'N/A' }}</td>
                                <td><strong>{{ number_format($ln->principal_amount, 2) }}</strong></td>
                                <td>{{ $ln->interest_rate ?? 0 }}%</td>
                                <td>{{ number_format($ln->total_amount ?? 0, 2) }}</td>
                                <td>{{ number_format($ln->monthly_installment ?? 0, 2) }}</td>
                                <td>{{ $ln->disbursement_date ? \Carbon\Carbon::parse($ln->disbursement_date)->format('d-M-Y') : '—' }}</td>
                                <td>
                                    <form action="{{ route('admin.approvals.loan.update', $ln->id) }}" method="POST" class="approve-form">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-success">✓ Approve</button>
                                    </form>
                                    <details class="action-details d-inline-block ms-1">
                                        <summary><span class="btn btn-sm btn-danger">✗ Reject</span></summary>
                                        <div class="reject-panel">
                                            <form action="{{ route('admin.approvals.loan.update', $ln->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <textarea name="admin_remark" class="form-control form-control-sm mb-1" rows="2" placeholder="Rejection reason" required></textarea>
                                                <button class="btn btn-sm btn-danger w-100">Submit Rejection</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No pending loan requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>{{-- .tab-content --}}
    </div>{{-- .card --}}
</div>

@push('js')
<script>
function switchTab(tabId) {
    var tab = document.querySelector('[href="#' + tabId + '"]');
    if (tab && window.bootstrap) {
        var bsTab = new bootstrap.Tab(tab);
        bsTab.show();
    }
}
// Auto-open tab with most pending items
document.addEventListener('DOMContentLoaded', function () {
    var counts = @json($pendingCounts);
    var max = 0, maxKey = 'tab-attendance';
    var map = {
        attendance: 'tab-attendance',
        leave: 'tab-leave',
        overtime: 'tab-overtime',
        convenience: 'tab-convenience',
        advance: 'tab-advance',
        loan: 'tab-loan'
    };
    for (var k in counts) {
        if (counts[k] > max) { max = counts[k]; maxKey = map[k]; }
    }
    if (max > 0) switchTab(maxKey);
});
</script>
@endpush
@endsection
