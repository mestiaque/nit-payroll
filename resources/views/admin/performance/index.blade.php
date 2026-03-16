@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Performance') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Employee Performance</h3>
            <div class="dropdown">
                <a href="{{ route('admin.performance.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Emp ID</th>
                        <th>Year</th>
                        <th>Month</th>
                        <th>Rating</th>
                        <th>Attendance / Leave Report</th>
                        <th>Dress / Behavior</th>
                        <th>Reviewer</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($performances as $perf)
                    <tr>
                        <td class="d-flex  align-items-center">{!! $perf->user->getAvt() !!} {{ $perf->user->name ?? 'N/A' }}</td>
                        <td>{{ $perf->user->employee_id ?? 'N/A' }}</td>
                        <td>{{ $perf->year }}</td>
                        <td>
                            @if($perf->report_month)
                                {{ \Carbon\Carbon::create($perf->year, $perf->report_month, 1)->format('F') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $perf->rating }}/5</td>
                        <td>
                            <div><strong>Period:</strong> {{ optional($perf->report_start_date)->format('d M Y') ?? '-' }} - {{ optional($perf->report_end_date)->format('d M Y') ?? '-' }}</div>
                            <div><strong>P:</strong> {{ $perf->present_days ?? 0 }}, <strong>L:</strong> {{ $perf->late_days ?? 0 }}, <strong>A:</strong> {{ $perf->absent_days ?? 0 }}, <strong>Leave:</strong> {{ $perf->leave_days ?? 0 }}</div>
                            <div><strong>Approved Leave Req:</strong> {{ $perf->approved_leave_requests ?? 0 }}</div>
                        </td>
                        <td>
                            <div><strong>Dress:</strong> {{ number_format((float) ($perf->dress_score ?? 0), 1) }}/5</div>
                            <div><strong>Behavior:</strong> {{ number_format((float) ($perf->behavior_score ?? 0), 1) }}/5</div>
                            @if($perf->dress_note)
                                <div><strong>Dress Note:</strong> {{ $perf->dress_note }}</div>
                            @endif
                            @if($perf->behavior_note)
                                <div><strong>Behavior Note:</strong> {{ $perf->behavior_note }}</div>
                            @endif
                        </td>
                        <td>{{ $perf->reviewer->name ?? 'N/A' }}</td>
                        <td><span class="badge badge-success">{{ ucfirst($perf->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
