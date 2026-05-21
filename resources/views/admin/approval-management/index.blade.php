@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Approvals') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card mb-3 mt-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">Approval Center — Pending</h5>
            <div>
                <a href="{{ route('admin.attendance-approval.create') }}" class="btn btn-sm btn-outline-primary">+ Attendance Request</a>
                <a href="{{ route('admin.approvals.completed') }}" class="btn btn-sm btn-dark">Completed</a>
            </div>
        </div>
        <div class="card-body pb-2">
            <ul class="nav nav-pills flex-wrap gap-1">
                @foreach(['all' => 'All', 'attendance' => 'Attendance', 'leave' => 'Leave', 'conveyance' => 'Conveyance', 'overtime' => 'Overtime'] as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $filter === $key ? 'active' : '' }}"
                           href="{{ route('admin.approvals.index', ['type' => $key]) }}">
                            {{ $label }}
                            <span class="badge bg-light text-dark">{{ $counts[$key] ?? 0 }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Employee</th>
                        <th>Summary</th>
                        <th>Details</th>
                        <th>Reason</th>
                        <th style="min-width:200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><span class="badge bg-info">{{ $item['type_label'] }}</span></td>
                            <td>{{ $item['employee'] }}</td>
                            <td>{{ $item['summary'] }}</td>
                            <td><small>{{ $item['detail'] }}</small></td>
                            <td><small>{{ Str::limit($item['reason'] ?? '-', 80) }}</small></td>
                            <td>
                                <form action="{{ route('admin.approvals.approve', [$item['type'], $item['id']]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $item['type'] }}{{ $item['id'] }}">Reject</button>

                                @if($item['type'] === 'attendance')
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#editAtt{{ $item['id'] }}">Edit</button>
                                @endif
                            </td>
                        </tr>

                        <div class="modal fade" id="rejectModal{{ $item['type'] }}{{ $item['id'] }}" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <form action="{{ route('admin.approvals.reject', [$item['type'], $item['id']]) }}" method="POST">
                                        @csrf
                                        <div class="modal-header"><h6 class="modal-title">Reject</h6>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                        <div class="modal-body">
                                            <textarea name="remark" class="form-control form-control-sm" rows="3" placeholder="Reject reason (optional)"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-sm btn-danger">Confirm Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if($item['type'] === 'attendance')
                            @php $approval = $item['meta']; @endphp
                            <div class="modal fade" id="editAtt{{ $item['id'] }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.approvals.attendance.edit', $item['id']) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-header"><h6>Edit before approve</h6>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                            <div class="modal-body row g-2">
                                                <div class="col-md-6">
                                                    <label>Status</label>
                                                    <select name="requested_status" class="form-control form-control-sm" required>
                                                        @foreach(['present','late','absent','leave'] as $st)
                                                            <option value="{{ $st }}" @selected(strtolower($approval->requested_status) === $st)>{{ ucfirst($st) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>In</label>
                                                    <input type="time" name="in_time" class="form-control form-control-sm" value="{{ $approval->in_time?->format('H:i') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Out</label>
                                                    <input type="time" name="out_time" class="form-control form-control-sm" value="{{ $approval->out_time?->format('H:i') }}">
                                                </div>
                                                <div class="col-12">
                                                    <label>Reason</label>
                                                    <input type="text" name="reason" class="form-control form-control-sm" value="{{ $approval->reason }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No pending approvals. All clear.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
