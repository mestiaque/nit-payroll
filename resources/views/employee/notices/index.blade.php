


@extends(employeeTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Notices') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Notices List</h3>
                </div>
                <div class="card-body">
                    @if($notices->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Priority</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notices as $notice)
                                <tr>
                                    <td><strong>{{ $notice->title }}</strong></td>
                                    <td>{{ $notice->notice_date->format('d M, Y') }}</td>
                                    <td>
                                        @if($notice->priority == 'high')
                                            <span class="badge badge-danger">High</span>
                                        @elseif($notice->priority == 'medium')
                                            <span class="badge badge-warning">Medium</span>
                                        @else
                                            <span class="badge badge-info">Low</span>
                                        @endif
                                    </td>
                                    <td>{{ $notice->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info">
                            No notices available at this time.
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    {{ $notices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
