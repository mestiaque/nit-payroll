@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Monthly Attendance Report') }}</title>
@endsection

@push('css')
<style>
    .report-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #4ecdc4; color: white; }
    .day-cell { text-align: center; font-size: 11px; padding: 5px !important; }
    .present { background: #d4edda; }
    .absent { background: #f8d7da; }
    .leave { background: #fff3cd; }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area">
    <h1>Monthly Attendance Report</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Attendance</li>
        <li class="item">Monthly Report</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <div class="report-card">
        <form action="{{ route('admin.attendance.monthly.report') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Month</label>
                <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i> Generate</button>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="button" onclick="window.print()" class="btn btn-success w-100">
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th rowspan="2">SL</th>
                        <th rowspan="2">Employee</th>
                        <th rowspan="2">Dept</th>
                        @for($day = 1; $day <= $daysInMonth ?? 31; $day++)
                        <th class="day-cell">{{ $day }}</th>
                        @endfor
                        <th rowspan="2">P</th>
                        <th rowspan="2">A</th>
                        <th rowspan="2">L</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyData ?? [] as $i => $employee)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($employee->photo)
                                    <img src="{{ asset('uploads/user_photo/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 8px;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($employee->id ?? 0) }}; margin-right: 8px; flex-shrink: 0;">
                                        {{ strtoupper(substr($employee->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <small class="font-weight-bold">{{ $employee->name }}</small><br>
                                    <small class="text-muted">{{ $employee->employee_id }}</small>
                                </div>
                            </div>
                        </td>
                        <td><small>{{ $employee->department_name ?? 'N/A' }}</small></td>
                        @for($day = 1; $day <= $daysInMonth ?? 31; $day++)
                        <td class="day-cell {{ $employee->attendance[$day] ?? '' }}">
                            @if(isset($employee->attendance[$day]))
                                @if($employee->attendance[$day] == 'present')
                                    <span class="text-success">P</span>
                                @elseif($employee->attendance[$day] == 'absent')
                                    <span class="text-danger">A</span>
                                @elseif($employee->attendance[$day] == 'leave')
                                    <span class="text-warning">L</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        @endfor
                        <td class="text-success"><strong>{{ $employee->present_count ?? 0 }}</strong></td>
                        <td class="text-danger"><strong>{{ $employee->absent_count ?? 0 }}</strong></td>
                        <td class="text-warning"><strong>{{ $employee->leave_count ?? 0 }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ ($daysInMonth ?? 31) + 6 }}" class="text-center text-muted py-4">
                            No attendance data available for selected month
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
