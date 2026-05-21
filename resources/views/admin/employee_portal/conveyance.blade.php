@extends('admin.layouts.master')
@section('title', 'Conveyance Report')
@section('main-content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Conveyance Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.employee.portal.dashboard') }}">Employee Portal</a></li>
                            <li class="breadcrumb-item active">Conveyance</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtered Conveyance List</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.employee.portal.conveyance') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="type" class="form-control">
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
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                                </div>
                                <div class="col-md-3 text-right">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.employee.portal.conveyance') }}" class="btn btn-secondary">Reset</a>
                                    <a href="{{ route('admin.employee.portal.conveyance.print', request()->query()) }}" target="_blank" class="btn btn-success">Print</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Reason</th>
                                        <th>Admin Remark</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $request)
                                        <tr>
                                            <td>{{ $requests->firstItem() + $loop->index }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $request->type)) }}</td>
                                            <td>{{ number_format($request->amount, 2) }}</td>
                                            <td>{{ ucfirst($request->status) }}</td>
                                            <td>{{ $request->payment_status ? ucfirst($request->payment_status) : '--' }}</td>
                                            <td>{{ $request->reason ?: '--' }}</td>
                                            <td>{{ $request->admin_remark ?: '--' }}</td>
                                            <td>{{ $request->created_at?->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No conveyance records found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection