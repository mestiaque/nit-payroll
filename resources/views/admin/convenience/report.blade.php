@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Conveyance Report') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card mt-3 mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Conveyance Report</h5>
            <a href="{{ route('admin.convenience.index') }}" class="btn btn-sm btn-secondary">Requests</a>
        </div>
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="small">From</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="small">To</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="small">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach(['pending','approved','rejected'] as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small">Payment</label>
                    <select name="payment_status" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="paid" @selected(request('payment_status') === 'paid')>Paid</option>
                        <option value="unpaid" @selected(request('payment_status') === 'unpaid')>Unpaid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small">Employee</label>
                    <select name="user_id" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(request('user_id') == $emp->id)>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-2"><div class="card p-2 text-center"><small>Total</small><strong>{{ $summary['total_count'] }}</strong></div></div>
        <div class="col-md-2"><div class="card p-2 text-center"><small>Pending</small><strong>{{ $summary['pending'] }}</strong></div></div>
        <div class="col-md-2"><div class="card p-2 text-center"><small>Approved ৳</small><strong>{{ number_format($summary['approved'], 0) }}</strong></div></div>
        <div class="col-md-2"><div class="card p-2 text-center"><small>Paid ৳</small><strong>{{ number_format($summary['paid'], 0) }}</strong></div></div>
        <div class="col-md-2"><div class="card p-2 text-center"><small>Unpaid ৳</small><strong>{{ number_format($summary['unpaid'], 0) }}</strong></div></div>
        <div class="col-md-2"><div class="card p-2 text-center"><small>Rejected</small><strong>{{ $summary['rejected'] }}</strong></div></div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Dept</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $row)
                        <tr>
                            <td>{{ $row->created_at?->format('d M Y') }}</td>
                            <td>{{ $row->user->name ?? '-' }}</td>
                            <td>{{ $row->user->department->name ?? '-' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $row->type)) }}</td>
                            <td>৳{{ number_format($row->amount, 2) }}</td>
                            <td><span class="badge bg-{{ $row->status === 'approved' ? 'success' : ($row->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($row->status) }}</span></td>
                            <td>{{ $row->payment_status ? ucfirst($row->payment_status) : '-' }}</td>
                            <td>{{ Str::limit($row->reason, 40) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No records in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
