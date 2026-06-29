@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Approvals - Completed') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')

<div class="breadcrumb-area">
    <h1>Approval Management</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Approval Management</li>
        <li class="item">Completed</li>
    </ol>
</div>

<div class="flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Completed Approvals (Approved / Rejected)</h5>
        <a href="{{ route('admin.approvals.index') }}" class="btn btn-sm btn-primary">
            <i class="bx bx-time"></i> View Pending
        </a>
    </div>

    <div class="card">
        <div class="card-header p-0">
            <ul class="nav nav-tabs" id="completedTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#c-attendance">Attendance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#c-leave">Leave</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#c-overtime">Overtime</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#c-convenience">Convenience</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#c-advance">Salary Advance</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#c-loan">Loan</a>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content p-3">

            {{-- Attendance --}}
            <div class="tab-pane fade show active" id="c-attendance">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr><th>#</th><th>Employee</th><th>Date</th><th>Original</th><th>Requested</th><th>Status</th><th>Admin Remark</th><th>Actioned At</th></tr>
                        </thead>
                        <tbody>
                        @forelse($attendanceCompleted as $a)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $a->user->name ?? 'N/A' }}</td>
                                <td>{{ $a->attendance_date?->format('d-M-Y') }}</td>
                                <td>{{ $a->original_status }}</td>
                                <td>{{ $a->requested_status }}</td>
                                <td>
                                    @if($a->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $a->admin_remark ?? '—' }}</td>
                                <td>{{ $a->updated_at?->format('d-M-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No completed attendance approvals.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Leave --}}
            <div class="tab-pane fade" id="c-leave">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr><th>#</th><th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th><th>Rejection Reason</th><th>Actioned At</th></tr>
                        </thead>
                        <tbody>
                        @forelse($leaveCompleted as $lv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $lv->user->name ?? 'N/A' }}</td>
                                <td>{{ $lv->leaveType->name ?? 'N/A' }}</td>
                                <td>{{ $lv->start_date?->format('d-M-Y') }}</td>
                                <td>{{ $lv->end_date?->format('d-M-Y') }}</td>
                                <td>{{ $lv->days }}</td>
                                <td>
                                    @if($lv->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $lv->rejection_reason ?? '—' }}</td>
                                <td>{{ $lv->updated_at?->format('d-M-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted">No completed leave approvals.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Overtime --}}
            <div class="tab-pane fade" id="c-overtime">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr><th>#</th><th>Employee</th><th>Date</th><th>Hours</th><th>Amount</th><th>Status</th><th>Rejection Reason</th><th>Actioned At</th></tr>
                        </thead>
                        <tbody>
                        @forelse($overtimeCompleted as $ot)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ot->user->name ?? 'N/A' }}</td>
                                <td>{{ $ot->overtime_date?->format('d-M-Y') }}</td>
                                <td>{{ number_format($ot->hours, 2) }}</td>
                                <td>{{ number_format($ot->amount, 2) }}</td>
                                <td>
                                    @if($ot->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $ot->rejection_reason ?? '—' }}</td>
                                <td>{{ $ot->updated_at?->format('d-M-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No completed overtime approvals.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Convenience --}}
            <div class="tab-pane fade" id="c-convenience">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr><th>#</th><th>Employee</th><th>Type</th><th>Amount</th><th>Status</th><th>Admin Remark</th><th>Actioned At</th></tr>
                        </thead>
                        <tbody>
                        @forelse($convenienceComplete as $cv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $cv->user->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($cv->type) }}</td>
                                <td>{{ number_format($cv->amount, 2) }}</td>
                                <td>
                                    @if($cv->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $cv->admin_remark ?? '—' }}</td>
                                <td>{{ $cv->updated_at?->format('d-M-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No completed convenience approvals.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Salary Advance --}}
            <div class="tab-pane fade" id="c-advance">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr><th>#</th><th>Employee</th><th>Requested</th><th>Approved Amt</th><th>Status</th><th>Actioned At</th></tr>
                        </thead>
                        <tbody>
                        @forelse($salaryAdvanceCompleted as $sa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sa->user->name ?? 'N/A' }}</td>
                                <td>{{ number_format($sa->requested_amount, 2) }}</td>
                                <td>{{ $sa->approved_amount ? number_format($sa->approved_amount, 2) : '—' }}</td>
                                <td>
                                    @if($sa->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $sa->approved_at ? \Carbon\Carbon::parse($sa->approved_at)->format('d-M-Y H:i') : $sa->updated_at?->format('d-M-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No completed salary advance requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Loan --}}
            <div class="tab-pane fade" id="c-loan">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr><th>#</th><th>Employee</th><th>Principal</th><th>Total</th><th>Monthly Inst.</th><th>Status</th><th>Actioned At</th></tr>
                        </thead>
                        <tbody>
                        @forelse($loanCompleted as $ln)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ln->user->name ?? 'N/A' }}</td>
                                <td>{{ number_format($ln->principal_amount, 2) }}</td>
                                <td>{{ number_format($ln->total_amount ?? 0, 2) }}</td>
                                <td>{{ number_format($ln->monthly_installment ?? 0, 2) }}</td>
                                <td>
                                    @if($ln->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $ln->approved_at ? \Carbon\Carbon::parse($ln->approved_at)->format('d-M-Y H:i') : $ln->updated_at?->format('d-M-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No completed loan requests.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
