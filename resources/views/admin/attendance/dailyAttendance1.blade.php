

@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Daily Attandance List')}}</title>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('public/admin/app-assets/vendors/css/tables/datatable/datatables.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('public/admin/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('public/admin/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}" />
<style type="text/css">

    .dataex-html5-export {
        width: 100% !important;
        table-layout: fixed;
    }
</style>
@endpush
@section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Daily Attandance List</h3>
         <div class="dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['sales']['add'])
             <!--<a href="{{route('admin.salesAction','create')}}" class="btn-custom primary" style="padding:5px 15px;">-->
             <!--    <i class="bx bx-plus"></i> Add Attandance-->
             <!--</a>-->
            @endisset

             <a href="{{route('admin.dailyAttendance')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

            <div class="row">
                <div class="col-md-6">
                <form action="{{route('admin.dailyAttendance')}}">
                    <div class="row">
                        <div class="col-md-3 mb-1">
                            <input type="date" name="startDate" value="{{ request()->startDate ?? date('Y-m-d') }}" class="form-control">
                        </div>

                        <div class="col-md-3 mb-1">
                            <input type="date" name="endDate" value="{{ request()->endDate ?? date('Y-m-d') }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-1">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ request()->search }}" placeholder="Search Employee ID, Name" class="form-control">
                                <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                            </div>
                        </div>
                    </div>
                </form>

                </div>
                <div class="col-md-6">
                    <ul class="statuslist p-0">
                        <li>All ({{ $total }})</li>
                        <li>Present ({{ $present }})</li>
                        <li>Late ({{ $late }})</li>
                        <li>Absent ({{ $absent }})</li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataex-html5-export">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Employee</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Work Hr.</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Map</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['in_time'] }}</td>
                            <td>{{ $row['out_time'] }}</td>
                            <td>{{ $row['work_hr'] }}</td>
                            <td>

                                @if($row['status']=='Present')
                                <span class="badge badge-success">
                                @elseif($row['status']=='Late')
                                <span class="badge badge-warning">
                                @elseif($row['status']=='Present')
                                <span class="badge badge-info">
                                @else
                                <span class="badge badge-danger">
                                @endif
                                {{ $row['status'] }}
                                </span>
                            </td>
                            <td>{{ $row['date'] }}</td>
                            <td>
                                @if($row['map_url'])
                                    <a href="{{ $row['map_url'] }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

               <div class="mt-3">
                {{ $users->links('pagination') }}
            </div>

            </div>


    </div>
</div>
</div>

@push('js')
<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('public/admin/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('public/admin/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('public/admin/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/admin/app-assets/vendors/js/tables/jszip.min.js')}}"></script>
<script src="{{asset('public/admin/app-assets/vendors/js/tables/pdfmake.min.js')}}"></script>
<script src="{{asset('public/admin/app-assets/vendors/js/tables/vfs_fonts.js')}}"></script>
<script src="{{asset('public/admin/app-assets/vendors/js/tables/buttons.html5.min.js')}}"></script>
<script src="{{asset('public/admin/app-assets/vendors/js/tables/buttons.print.min.js')}}"></script>
<script src="{{asset('public/admin/app-assets/vendors/js/tables/buttons.colVis.min.js')}}"></script>

<!-- END: Page Vendor JS-->

<script type="text/javascript">
    $(document).ready(function () {

    if ($.fn.DataTable.isDataTable('.dataex-html5-export')) {
        $('.dataex-html5-export').DataTable().destroy();
    }

    $('.dataex-html5-export').DataTable({
        dom: "Bfrtip",
        buttons: [
            "copyHtml5",
            "excelHtml5",
            "csvHtml5",
            {
                extend: "pdfHtml5",
                orientation: "landscape",
                pageSize: "LEGAL"
            },
            "print"
        ],

        pageLength: 100,
        lengthMenu: [25, 50, 100, 200, 500],

        autoWidth: false,
        responsive: false,
        scrollX: false
    });

});
</script>
@endpush
@endsection
