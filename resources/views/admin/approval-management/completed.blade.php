@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Completed Approvals') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card mb-4 mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Approval Management - Completed</h5>
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-arrow-left"></i> Back to Pending
            </a>
        </div>
        <div class="card-body">
            <p class="mb-0">Only approved and rejected items are shown here.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Attendance Edit - Completed List</h5>
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
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceCompleted as $approval)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $approval->user->name ?? 'N/A' }}</td>
                                <td>{{ $approval->attendance_date ? $approval->attendance_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $approval->original_status ?? 'N/A' }}</td>
                                <td>{{ $approval->requested_status }}</td>
                                <td>
                                    @if($approval->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No completed attendance request found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Leave - Completed List</h5>
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
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveCompleted as $leave)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $leave->user->name ?? 'N/A' }}</td>
                                <td>{{ $leave->leaveType->name ?? 'N/A' }}</td>
                                <td>{{ $leave->start_date ? $leave->start_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $leave->end_date ? $leave->end_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    @if($leave->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No completed leave approval found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Convenience - Completed List</h5>
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
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($convenienceComplete as $request)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $request->user->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($request->type) }}</td>
                                <td>{{ number_format($request->amount, 2) }}</td>
                                <td>
                                    @if($request->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->status == 'approved')
                                        @if(($request->payment_status ?? 'unpaid') == 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-warning">Unpaid</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $request->created_at ? $request->created_at->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    @if($request->status == 'approved' && ($request->payment_status ?? 'unpaid') != 'paid')
                                        <details>
                                            <summary class="btn btn-sm btn-primary" style="list-style:none;cursor:pointer;">Mark Paid</summary>
                                            <form action="{{ route('admin.convenience.payment', $request->id) }}" method="POST" class="mt-2" style="min-width:260px;">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-1">
                                                    <select name="payment_method" class="form-control form-control-sm" required>
                                                        <option value="">Select Method</option>
                                                        <option value="cash">Cash</option>
                                                        <option value="bank">Bank</option>
                                                        <option value="mobile_banking">Mobile Banking</option>
                                                    </select>
                                                </div>
                                                <div class="mb-1">
                                                    <input type="text" name="payment_note" class="form-control form-control-sm" placeholder="Payment note (optional)">
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-success w-100">Confirm Payment</button>
                                            </form>
                                        </details>
                                    @elseif($request->status == 'approved')
                                        <span class="text-muted">Paid</span>
                                    @else
                                        <span class="text-muted">No action</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No completed convenience request found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
