@extends(employeeTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Conveyance Request') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Conveyance Requests</h5>
            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addConveyanceModal">
                <i class="fa fa-plus"></i> Add Request
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
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
                                <td>
                                    @if($request->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($request->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->payment_status === 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($request->payment_status === 'unpaid')
                                        <span class="badge badge-secondary">Unpaid</span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>{{ $request->reason ?: '--' }}</td>
                                <td>{{ $request->admin_remark ?: '--' }}</td>
                                <td>{{ $request->created_at?->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No conveyance request found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $requests->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="addConveyanceModal" tabindex="-1" role="dialog" aria-labelledby="addConveyanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addConveyanceModalLabel">Add Conveyance Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('customer.conveyance.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="conveyance">Conveyance</option>
                            <option value="travel">Travel</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Write request details"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
