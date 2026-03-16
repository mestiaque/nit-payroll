@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Retirement') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Retirement Management</h3>
            <a href="{{ route('admin.retirement.create') }}" class="btn btn-sm   btn-primary"><i class="fa fa-plus"></i> Add New</a>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Emp ID</th>
                        <th>Retirement Date</th>
                        <th>Type</th>
                        <th>Settlement Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($retirements as $ret)
                    <tr>
                        <td class="d-flex align-items-center">{!! $ret->user->getAvt() !!} {{ $ret->user->name ?? 'N/A' }}</td>
                        <td>{{ $ret->user->employee_id ?? 'N/A' }}</td>
                        <td>{{ $ret->retirement_date->format('d M Y') }}</td>
                        <td>{{ ucfirst($ret->type) }}</td>
                        <td>{{ number_format($ret->settlement_amount, 2) }}</td>
                        <td>
                            @if($ret->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($ret->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-info">Processed</span>
                            @endif
                        </td>
                        <td>
                            @if($ret->status == 'pending')
                            <form action="{{ route('admin.retirement.update', $ret->id) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
