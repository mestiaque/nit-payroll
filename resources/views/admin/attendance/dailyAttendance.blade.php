@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Daily Attendance List') }}</title>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('/admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('/admin/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('/admin/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css') }}" />
<style>
    /* Premium Card / Filter Styling */
    .attendance-filters {
        background: ;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .statuslist {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }
    .statuslist li {
        background: #e9ecef;
        padding: 5px 12px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.875rem;
    }
        ul.statuslist li::before {
        content: none !important;  /* Remove any bullet / icon */
    }
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 10px;
    }
    .dataTables_filter{display:none;}
    table.table thead{background : #56d2ff;}
</style>
@endpush

@section('contents')

<div class="flex-grow-1">
    @include(adminTheme().'alerts')
    <!-- Filters -->
    <div class="attendance-filters pt-0">
        <form action="{{ route('admin.dailyAttendance') }}" method="GET" class="row g-3 align-items-end">


                <!-- Stats -->
            <div class="row mb-3 w-100">
                {{-- Employees --}}
                <div class="col-md-3">
                    <div class="card shadow-sm border-0" style="background: #007bff38">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px;height:50px;">
                                <i class="fa fa-users"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $total ?? 0 }}</h4>
                                <small class="text-muted">Total Employees</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Present Employees --}}
                <div class="col-md-3">
                    <div class="card shadow-sm border-0" style="background: #28a74538">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px;height:50px;">
                                <i class="fa fa-user-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $present ?? 0 }}</h5>
                                <small class="text-muted">Present Employees</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Late Employees --}}
                <div class="col-md-3">
                    <div class="card shadow-sm border-0" style="background: #ffc10738">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px;height:50px;">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $late ?? 0 }}</h5>
                                <small class="text-muted">Late Employees</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Absent Employees --}}
                <div class="col-md-3">
                    <div class="card shadow-sm border-0" style="background: #dc354538">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px;height:50px;">
                                <i class="fa fa-user-times"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $absent ?? 0 }}</h4>
                                <small class="text-muted">Absent Employees</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <hr class="text-muted">

            <div class="col-md-2">
                <label for="startDate" class="form-label mb-0">Start Date</label>
                <input type="date" name="startDate" value="{{ request()->startDate ?? date('Y-m-d') }}" class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label for="endDate" class="form-label mb-0">End Date</label>
                <input type="date" name="endDate" value="{{ request()->endDate ?? date('Y-m-d') }}" class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label for="employeeId" class="form-label mb-0">Employee ID</label>
                <input type="text" name="employeeId" value="{{ request()->employeeId }}" class="form-control form-control-sm" placeholder="ID">
            </div>

            <div class="col-md-2">
                <label for="search" class="form-label mb-0">Employee Name</label>
                <input type="text" name="search" value="{{ request()->search }}" class="form-control form-control-sm" placeholder="Name">
            </div>

            <div class="col-md-2">
                <label for="designation" class="form-label mb-0">Designation</label>
                <select name="designation" class="form-control form-control-sm">
                    <option value="">All Designations</option>
                    @foreach($designations as $des)
                        <option value="{{ $des->id }}" @if(request()->designation==$des->id) selected @endif>{{ $des->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="department" class="form-label mb-0">Department</label>
                <select name="department" class="form-control form-control-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @if(request()->department==$dept->id) selected @endif>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="employeeType" class="form-label mb-0">Employee Type</label>
                <select name="employeeType" class="form-control form-control-sm">
                    <option value="">All Types</option>
                    @foreach($employeeTypes as $type)
                        <option value="{{ $type->id }}" @if(request()->employeeType==$type->id) selected @endif>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label mb-0">Status</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    <option value="Present" @if(request()->status=='Present') selected @endif>Present</option>
                    <option value="Late" @if(request()->status=='Late') selected @endif>Late</option>
                    <option value="Absent" @if(request()->status=='Absent') selected @endif>Absent</option>
                </select>
            </div>

            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i> Search</button>
                <a href="{{ route('admin.dailyAttendance') }}" class="btn btn-warning btn-sm"><i class="bx bx-rotate-left"></i> Reset</a>
            </div>

        </form>
    </div>




    <!-- Summary -->


    <!-- Attendance Table -->
    <div class="card shadow-sm w-100">
        <div class="card-body">
            <!-- Add print btn -->
            <div class="mb-3 text-end">
                <a href="{{ route('admin.dailyAttendancePrint', request()->query()) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="bx bx-printer"></i> Print
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dataex-html5-export w-100">
                    <thead class="table-darkx">
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>ID</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Employee Type</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Work Hr.</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Map</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($finalData as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['employee_id'] ?? '--' }}</td>
                            <td>{{ $row['designation'] ?? '--' }}</td>
                            <td>{{ $row['department'] ?? '--' }}</td>
                            <td>{{ $row['employee_type'] ?? '--' }}</td>
                            <td>{{ $row['in_time'] }}</td>
                            <td>{{ $row['out_time'] }}</td>
                            <td>{{ $row['work_hr'] }}</td>
                            <td>
                                <span class="badge
                                    @if($row['status']=='Present') badge-success
                                    @elseif($row['status']=='Late') bg-warning
                                    @elseif($row['status']=='Holiday') bg-info
                                    @elseif($row['status']=='Present') bg-info
                                    @else bg-danger
                                    @endif text-white">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td>{{ $row['date'] }}    <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($row['date'])->format('l') }}
                            </small></td>
                            <td>
                                @if($row['map_url'])
                                <a href="{{ $row['map_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                @else -- @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="12" class="text-center">No attendance found</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    @if($users->hasPages())
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                            </div>
                            {{ $users->appends(request()->all())->links('pagination') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="{{ asset('admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/jszip.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/pdfmake.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/vfs_fonts.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/buttons.html5.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/buttons.print.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/buttons.colVis.min.js') }}"></script>

<script>
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('.dataex-html5-export')) {
        $('.dataex-html5-export').DataTable().destroy();
    }

    $('.dataex-html5-export').DataTable({
        dom: "Bfrtip",
        // buttons: [
        //     { extend: "copyHtml5", className: 'btn btn-sm btn-outline-secondary text-white mr-1' },
        //     { extend: "excelHtml5", className: 'btn btn-sm btn-outline-success  text-white mr-1' },
        //     { extend: "csvHtml5", className: 'btn btn-sm btn-outline-info  text-white mr-1' },
        //     { extend: "pdfHtml5", className: 'btn btn-sm btn-outline-danger  text-white mr-1', orientation: "landscape", pageSize: "LEGAL" },
        //     { extend: "print", className: 'btn btn-sm btn-outline-primary  text-white mr-1' }
        // ],
        pageLength: 50,
        lengthMenu: [25, 50, 100, 200],
        responsive: true,
        scrollX: true,
        autoWidth: false
    });
});
</script>
@endpush
@endsection
