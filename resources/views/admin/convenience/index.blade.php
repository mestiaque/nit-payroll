@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Convenience Request') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card">
        <div class="card-header ">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-0">Convenience List</h5>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('admin.convenience.create') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus"></i> Add New
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.convenience.index') }}" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-control form-control-sm">
                            <option value="">All Employees</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (string) ($filters['employee_id'] ?? '') === (string) $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            <option value="conveyance" {{ ($filters['type'] ?? '') === 'conveyance' ? 'selected' : '' }}>Conveyance</option>
                            <option value="travel" {{ ($filters['type'] ?? '') === 'travel' ? 'selected' : '' }}>Travel</option>
                            <option value="other" {{ ($filters['type'] ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                            <option value="salary_advance" {{ ($filters['type'] ?? '') === 'salary_advance' ? 'selected' : '' }}>Salary Advance</option>
                            <option value="loan" {{ ($filters['type'] ?? '') === 'loan' ? 'selected' : '' }}>Loan</option>
                            <option value="transfer" {{ ($filters['type'] ?? '') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Status</option>
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    @if($hasPaymentStatusColumn)
                        <div class="col-md-2">
                            <label class="form-label">Payment</label>
                            <select name="payment_status" class="form-control form-control-sm">
                                <option value="">All Payment</option>
                                <option value="none" {{ ($filters['payment_status'] ?? '') === 'none' ? 'selected' : '' }}>Not Set</option>
                                <option value="unpaid" {{ ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="paid" {{ ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                    @endif
                    <div class="col-md-2">
                        <label class="form-label">From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm btn-primary mr-2 px-4">Filter</button>
                        <a href="{{ route('admin.convenience.index') }}" class="btn btn-secondary btn-sm mr-2">Reset</a>
                    <a href="{{ route('admin.convenience.print', request()->query()) }}" target="_blank" class="btn btn-success btn-sm">
                        <i class="fa fa-print"></i> Print
                    </a>
                    </div>
                </div>

            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            @if($hasPaymentStatusColumn)
                                <th>Payment</th>
                            @endif
                            <th>Date</th>
                            <th>Remark</th>
                            @if($hasPaymentStatusColumn)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $item)
                        <tr>
                            <td>{{ $requests->firstItem() + $loop->index }}</td>
                            <td>{{ $item->user->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $item->type)) }}</td>
                            <td>{{ number_format($item->amount, 2) }}</td>
                            <td>
                                @if($item->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                            @if($hasPaymentStatusColumn)
                                <td>{{ $item->payment_status ? ucfirst($item->payment_status) : '--' }}</td>
                            @endif
                            <td>{{ $item->created_at?->format('d M Y') }}</td>
                            <td>{{ $item->admin_remark ?: '--' }}</td>
                            @if($hasPaymentStatusColumn)
                                <td>
                                    @if($item->status === 'approved')
                                        <form action="{{ route('admin.convenience.payment', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success" {{ $item->payment_status === 'paid' ? 'disabled' : '' }}>
                                                Paid
                                            </button>
                                        </form>
                                    @else
                                        --
                                    @endif
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $hasPaymentStatusColumn ? 10 : 7 }}" class="text-center">No convenience request found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
