@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Job Card') }}</title>
@endsection

@push('css')

@endpush

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1 jobcard-shell">

    <!-- Filter Section -->
    <div class="card no-print jobcard-panel">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Job Card</h5>
            <div>
                {{-- <a href="{{route('admin.holiday.create')}}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Holiday</a> --}}
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.jobcard.print') }}" target="_blank" class="jobcard-form-grid">
                <div class="row">
                    <div class="col-md-3">
                        <label class="jobcard-label">Employee ID(s) <span class="jobcard-optional">Optional</span></label>
                        <input type="text" name="user_ids" class="form-control jobcard-input" placeholder="e.g., 1,5,10 or E001,E005" value="{{ request('user_ids', '') }}">
                        <small class="jobcard-help">Leave empty to print all employees.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="jobcard-label">Department</label>
                        <select name="department_id" class="form-control jobcard-select">
                            <option value="">All Departments</option>
                            @foreach(($departments ?? collect()) as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="jobcard-label">Section</label>
                        <select name="section_id" class="form-control jobcard-select">
                            <option value="">All Sections</option>
                            @foreach(($sections ?? collect()) as $section)
                                <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="jobcard-label">Designation</label>
                        <select name="designation_id" class="form-control jobcard-select">
                            <option value="">All Designations</option>
                            @foreach(($designations ?? collect()) as $designation)
                                <option value="{{ $designation->id }}" {{ request('designation_id') == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="jobcard-label">Employee Type</label>
                        <select name="employee_type" class="form-control jobcard-select">
                            <option value="">All Employee Types</option>
                            @foreach(($employeeTypes ?? collect()) as $employeeType)
                                <option value="{{ $employeeType->id }}" {{ request('employee_type') == $employeeType->id ? 'selected' : '' }}>{{ $employeeType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="jobcard-label">Shift</label>
                        <select name="shift_id" class="form-control jobcard-select">
                            <option value="">All Shifts</option>
                            @foreach(($shifts ?? collect()) as $shift)
                                <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name_of_shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="jobcard-label">Month</label>
                        <input type="month" name="month" class="form-control jobcard-input" value="{{ $month }}" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary mt-4">
                            <i class="fa fa-print"></i> Print Job Cards
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


</div>
@endsection
