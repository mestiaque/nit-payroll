@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Policy') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Policy</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.policy.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="late_fine">Late Fine</option>
                        <option value="absent_fine">Absent Fine</option>
                        <option value="overtime_rate">Overtime Rate</option>
                        <option value="working_hour">Working Hour</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Value</label>
                    <input type="number" name="value" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Unit</label>
                    <select name="unit" class="form-control" required>
                        <option value="amount">Amount</option>
                        <option value="percentage">Percentage</option>
                        <option value="hours">Hours</option>
                        <option value="minutes">Minutes</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.policy.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
