@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Completed Approvals') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card mb-3 mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Completed Approvals</h5>
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-sm btn-primary">Back to Pending</a>
        </div>
        <div class="card-body pb-2">
            <ul class="nav nav-pills">
                @foreach(['all','attendance','leave','conveyance','overtime'] as $key)
                    <li class="nav-item">
                        <a class="nav-link {{ $filter === $key ? 'active' : '' }}"
                           href="{{ route('admin.approvals.completed', ['type' => $key]) }}">{{ ucfirst($key) }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Employee</th>
                        <th>Summary</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item['type_label'] }}</td>
                            <td>{{ $item['employee'] }}</td>
                            <td>{{ $item['summary'] }}</td>
                            <td>
                                <span class="badge bg-{{ $item['status'] === 'approved' ? 'success' : 'danger' }}">
                                    {{ ucfirst($item['status']) }}
                                </span>
                            </td>
                            <td>{{ $item['detail'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
