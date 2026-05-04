@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Manual Attendance')}}</title>
@endsection
@section('contents')
<div class="flex-grow-1">
    @include(adminTheme().'alerts')
    <div class="card mb-4 mt-3">
        <div class="card-header">
            <h5 class="mb-0">Late & Absent Attendance Update</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attendance.manual.index') }}" class="row g-3 mb-4">
                <div class="col-md-2">
                    <label for="user_id" class="form-label">Employee</label>
                    <select name="user_id" id="user_id" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ (string) $selectedUserId === (string) $employee->id ? 'selected' : '' }}>
                                {{ $employee->employeeInfo->name ?? $employee->name }} [{{ $employee->employeeInfo->employee_id ?? 'N/A' }}]
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ (string) $selectedDepartmentId === (string) $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ request('date') ?? $selectedDate }}">
                </div>
                <div class="col-md-2">
                    <label for="employee_id" class="form-label">Employee ID</label>
                    <input type="text" name="employee_id" id="employee_id" class="form-control form-control-sm" placeholder="e.g. 1010" value="{{ $selectedEmployeeId ?? '' }}">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('admin.attendance.manual.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                </div>
            </form>

            <hr class="my-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Late List ({{ $selectedDate }})</h6>
                <small class="text-muted">Select late employees and mark as present (approval required).</small>
            </div>
            <form method="POST" action="{{ route('admin.attendance.manual.bulkUpdate') }}" class="mb-4">
                @csrf
                <input type="hidden" name="date" value="{{ $selectedDate }}">
                <input type="hidden" name="action_type" value="late_to_present">
                <input type="hidden" name="user_id" value="{{ $selectedUserId }}">
                <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">

                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped mb-2">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="late_select_all"></th>
                                <th>#</th>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lateAttendances as $lateAttendance)
                                <tr>
                                    <td><input type="checkbox" name="user_ids[]" value="{{ $lateAttendance->user_id }}" class="late-checkbox"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lateAttendance?->user?->employee_id ?? 'N/A' }}</td>
                                    <td>{{ $lateAttendance?->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $lateAttendance?->user?->department?->name ?? 'N/A' }}</td>
                                    <td>{{ $lateAttendance->in_time ? \Carbon\Carbon::parse($lateAttendance->in_time)->format('H:i') : 'N/A' }}</td>
                                    <td>{{ $lateAttendance->out_time ? \Carbon\Carbon::parse($lateAttendance->out_time)->format('H:i') : 'N/A' }}</td>
                                    <td>{{ $lateAttendance->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No late employee found for selected date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label for="late_in_time" class="form-label">In Time</label>
                        <input type="time" name="in_time" id="late_in_time" class="form-control form-control-sm" value="09:00" required>
                    </div>
                    <div class="col-md-2">
                        <label for="late_out_time" class="form-label">Out Time</label>
                        <input type="time" name="out_time" id="late_out_time" class="form-control form-control-sm" value="19:00" required>
                    </div>
                    <div class="col-md-2">
                        <label for="late_requested_status" class="form-label">Set Status</label>
                        <select name="requested_status" id="late_requested_status" class="form-control form-control-sm" required>
                            <option value="Present">Present</option>
                            <option value="Late">Late</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="late_remarks" class="form-label">Remarks (Optional)</label>
                        <input type="text" name="remarks" id="late_remarks" class="form-control form-control-sm" placeholder="Reason for mark update">
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Update selected late employees?')">Update Late</button>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Absent List ({{ $selectedDate }})</h6>
                <small class="text-muted">Select absent employees and submit manual attendance request.</small>
            </div>
            <form method="POST" action="{{ route('admin.attendance.manual.bulkUpdate') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $selectedDate }}">
                <input type="hidden" name="action_type" value="absent_to_present">
                <input type="hidden" name="user_id" value="{{ $selectedUserId }}">
                <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">

                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped mb-2">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="absent_select_all"></th>
                                <th>#</th>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Department</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absentEmployees as $absentEmployee)
                                <tr>
                                    <td><input type="checkbox" name="user_ids[]" value="{{ $absentEmployee->id }}" class="absent-checkbox"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $absentEmployee->employee_id ?? 'N/A' }}</td>
                                    <td>{{ $absentEmployee->name }}</td>
                                    <td>{{ $absentEmployee->department->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No absent employee found for selected date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label for="bulk_in_time" class="form-label">In Time</label>
                        <input type="time" name="in_time" id="bulk_in_time" class="form-control form-control-sm" value="09:00" required>
                    </div>
                    <div class="col-md-2">
                        <label for="bulk_out_time" class="form-label">Out Time</label>
                        <input type="time" name="out_time" id="bulk_out_time" class="form-control form-control-sm" value="19:00" required>
                    </div>
                    <div class="col-md-2">
                        <label for="absent_requested_status" class="form-label">Set Status</label>
                        <select name="requested_status" id="absent_requested_status" class="form-control form-control-sm" required>
                            <option value="Present">Present</option>
                            <option value="Late">Late</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="absent_remarks" class="form-label">Remarks (Optional)</label>
                        <input type="text" name="remarks" id="absent_remarks" class="form-control form-control-sm" placeholder="Reason for manual attendance">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Submit attendance request for selected absent employees?')">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lateSelectAll = document.getElementById('late_select_all');
        const lateCheckboxes = document.querySelectorAll('.late-checkbox');

        if (lateSelectAll) {
            lateSelectAll.addEventListener('change', function() {
                lateCheckboxes.forEach((checkbox) => {
                    checkbox.checked = lateSelectAll.checked;
                });
            });
        }

        const absentSelectAll = document.getElementById('absent_select_all');
        const absentCheckboxes = document.querySelectorAll('.absent-checkbox');

        if (absentSelectAll) {
            absentSelectAll.addEventListener('change', function() {
                absentCheckboxes.forEach((checkbox) => {
                    checkbox.checked = absentSelectAll.checked;
                });
            });
        }

        document.querySelectorAll('form[action="{{ route('admin.attendance.manual.bulkUpdate') }}"]').forEach((form) => {
            form.addEventListener('submit', function(e) {
                const hasSelection = form.querySelector('input[name="user_ids[]"]:checked');
                if (!hasSelection) {
                    e.preventDefault();
                    alert('Please select at least one employee.');
                }
            });
        });
    });
</script>

@endsection
