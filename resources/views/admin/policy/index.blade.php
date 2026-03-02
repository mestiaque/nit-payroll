@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Policy') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Policy Settings</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.policy.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Unit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($policies as $policy)
                <tr>
                    <td>{{ $policy->name }}</td>
                    <td>{{ ucfirst($policy->type) }}</td>
                    <td>{{ $policy->value }}</td>
                    <td>{{ $policy->unit }}</td>
                    <td><span class="badge badge-{{ $policy->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($policy->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
