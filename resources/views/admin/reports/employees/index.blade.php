@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Employee Report') }}</title>
@endsection

@push('css')
<style>
    .emp-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #6c5ce7; color: white; }
    .stat-box { text-align: center; padding: 15px; border-radius: 8px; }
    .stat-box.total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .stat-box.active { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
    .stat-box.inactive { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: white; }
    .stat-box.retired { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
</style>
@endpush

@section('contents')

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <!-- Stats -->
    <div class="row mb-3">
        {{-- Employees --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #007bff38">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        <small class="text-muted">Total Employees</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Employees --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #28a74538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-user-check"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $stats['active'] ?? 0 }}</h5>
                        <small class="text-muted">Active</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deduction --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #dc354538">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-minus-circle"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $stats['inactive'] ?? 0 }}</h5>
                        <small class="text-muted">Inactive</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Retired --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background: #17a2b838">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:50px;height:50px;">
                        <i class="fa fa-user-tie"></i>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $stats['retired'] ?? 0 }}</h4>
                        <small class="text-muted">Retired</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="emp-card">
        <form action="{{ route('admin.reports.employees.index') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-2">
                <label>Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, ID, Phone, Email" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label>Department</label>
                <select name="department_id" class="form-control form-control-sm">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Designation</label>
                <select name="designation_id" class="form-control form-control-sm">
                    <option value="">All Designations</option>
                    @foreach($designations ?? [] as $desig)
                    <option value="{{ $desig->id }}" {{ request('designation_id') == $desig->id ? 'selected' : '' }}>
                        {{ $desig->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select name="employee_status" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('employee_status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('employee_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="retired" {{ request('employee_status') == 'retired' ? 'selected' : '' }}>Retired</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Gender</label>
                <select name="gender" class="form-control form-control-sm">
                    <option value="">All Gender</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-sm btn-primary mr-1"><i class="bx bx-search"></i>Search</button>
                <a href="{{ route('admin.reports.employees.index') }}" class="btn btn-sm btn-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Join Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees ?? [] as $key => $employee)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $employee->employee_id ?? 'N/A' }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->department->name ?? 'N/A' }}</td>
                        <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                        <td>{{ $employee->mobile ?? 'N/A' }}</td>
                        <td>{{ $employee->email ?? 'N/A' }}</td>
                        <td>{{ ucfirst($employee->gender ?? 'N/A') }}</td>
                        <td>{{ $employee->joining_date ? date('d-m-Y', strtotime($employee->joining_date)) : 'N/A' }}</td>
                        <td>
                            @if($employee->employee_status == 'active')
                            <span class="badge bg-success">Active</span>
                            @elseif($employee->employee_status == 'inactive')
                            <span class="badge bg-danger">Inactive</span>
                            @elseif($employee->employee_status == 'retired')
                            <span class="badge bg-warning">Retired</span>
                            @else
                            <span class="badge bg-secondary">{{ $employee->employee_status ?? 'N/A' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No employees found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
