
@extends(employeeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('My Attendance')}}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card">
        <form method="GET" action="">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="month">Month</label>
                    <input type="month" id="month" name="month" class="form-control form-control-sm" value="{{ request('month', date('Y-m')) }}">
                </div>
                <div class="col-md-3">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }} style="color:green">Present</option>
                        <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }} style="color:red">Absent</option>
                        <option value="Late" {{ request('status') == 'Late' ? 'selected' : '' }} style="color:orange">Late</option>
                        <option value="Leave" {{ request('status') == 'Leave' ? 'selected' : '' }} style="color:rgb(13, 104, 139)">Leave</option>
                    </select>
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('customer.myAttendance') }}" class="btn btn-secondary btn-sm"><i class="fas fa-sync"></i> Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Work Hour</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $attendances = array_reverse($attendances)
                    @endphp
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance['date'] }}</td>
                            <td>{{ $attendance['in_time'] }}</td>
                            <td>{{ $attendance['out_time'] }}</td>
                            <td>{{ $attendance['hour'] }}</td>
                            <td>
                                <span class="badge
                                    @if($attendance['status']=='Present') badge-success
                                    @elseif($attendance['status']=='Late') badge-warning
                                    @elseif($attendance['status']=='Absent') badge-danger
                                    @elseif($attendance['status']=='Leave') badge-info
                                    @else badge-secondary
                                    @endif">
                                    {{ $attendance['status'] }}
                                </span>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No attendance records found.</td>
                        </tr>
                    @endforelse
                    @if(count($attendances) > 0)
                        <tr>
                            <td colspan="5" class="text-center">
                                Total Days: <strong style="color:blue">{{ count($attendances) }}</strong> |
                                Present: <strong style="color:green">{{ $presentCount }}</strong> |
                                Late: <strong style="color:orange">{{ $lateCount }}</strong> |
                                Absent: <strong style="color:red">{{ $absentCount }}</strong> |
                                Leave: <strong style="color:rgb(13, 104, 139)">{{ $leaveCount }}</strong> |
                                Total Work Hours:<strong style="color:purple">{{ $totalHours }}</strong>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
