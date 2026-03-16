@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Convenience Request') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0">Convenience Request</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.convenience.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add New
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <h6 class="mb-2">Pending List</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests as $request)
                    <tr>
                        <td>{{ $request->user->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($request->type) }}</td>
                        <td>{{ number_format($request->amount, 2) }}</td>
                        <td>
                            @if($request->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($request->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.approvals.index') }}" class="btn btn-sm btn-info">Go to Approvals</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No pending request found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h6 class="mb-2 mt-4">Complete List</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completeRequests as $request)
                    <tr>
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
                        <td>{{ $request->created_at->format('d M Y') }}</td>
                        <td><a href="{{ route('admin.approvals.index') }}" class="btn btn-sm btn-outline-secondary">View in Approvals</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No complete request found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
