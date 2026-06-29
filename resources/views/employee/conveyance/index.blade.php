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
                            <th>Amount</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Travel By</th>
                            <th>Attachment</th>
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
                                <td>{{ number_format($request->amount, 2) }}</td>
                                <td>{{ $request->from_location ?: '--' }}</td>
                                <td>{{ $request->to_location ?: '--' }}</td>
                                <td>{{ $request->travel_by ?: '--' }}</td>
                                <td>
                                    @if($request->attachment)
                                        <a href="{{ asset('storage/'.$request->attachment) }}" target="_blank">View</a>
                                    @else
                                        --
                                    @endif
                                </td>
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
                                <td colspan="11" class="text-center">No conveyance request found.</td>
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
            <form action="{{ route('customer.conveyance.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="from_location">From</label>
                        <input type="text" name="from_location" id="from_location" class="form-control" placeholder="From location">
                    </div>
                    <div class="form-group">
                        <label for="to_location">To</label>
                        <input type="text" name="to_location" id="to_location" class="form-control" placeholder="To location">
                    </div>
                    <div class="form-group">
                        <label for="travel_by">Travel By</label>
                        <select name="travel_by" id="travel_by" class="form-control">
                            <option value="">-- Select --</option>
                            <option value="Rikshaw">Rikshaw</option>
                            <option value="Bus">Bus</option>
                            <option value="Ride Sharing: Car">Ride Sharing: Car</option>
                            <option value="Ride Sharing: Bike">Ride Sharing: Bike</option>
                            <option value="Personal Vehicle">Personal Vehicle</option>
                            <option value="Metro Rail">Metro Rail</option>
                            <option value="Bus & Rikshaw">Bus & Rikshaw</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" name="attachment" id="attachment" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <textarea name="reason" id="reason" class="form-control" rows="2" placeholder="Write request details"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
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
