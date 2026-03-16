@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Performance Review') }}</title>
@endsection

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Add Performance Review</h3>
            <a href="{{ route('admin.performance.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.performance.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Employee</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Year</label>
                        <select name="year" class="form-control" required>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Report Month</label>
                        <select name="report_month" class="form-control" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Rating (0-5)</label>
                        <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Dress Score (0-5)</label>
                        <input type="number" name="dress_score" class="form-control" step="0.1" min="0" max="5" placeholder="e.g. 4.5">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Behavior Score (0-5)</label>
                        <input type="number" name="behavior_score" class="form-control" step="0.1" min="0" max="5" placeholder="e.g. 4.2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dress Note</label>
                        <textarea name="dress_note" class="form-control" rows="2" placeholder="Dress code, grooming, uniform compliance"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Behavior Note</label>
                        <textarea name="behavior_note" class="form-control" rows="2" placeholder="Team behavior, discipline, communication"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Comments</label>
                        <textarea name="comments" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="mb-2">Attendance & Leave Report (Month-wise)</h6>
                            <div class="row" id="reportPreview">
                                <div class="col-md-2 col-6 mb-2"><strong>Present:</strong> <span data-key="present_days">0</span></div>
                                <div class="col-md-2 col-6 mb-2"><strong>Late:</strong> <span data-key="late_days">0</span></div>
                                <div class="col-md-2 col-6 mb-2"><strong>Absent:</strong> <span data-key="absent_days">0</span></div>
                                <div class="col-md-2 col-6 mb-2"><strong>Leave:</strong> <span data-key="leave_days">0</span></div>
                                <div class="col-md-4 col-12 mb-2"><strong>Approved Leave Requests:</strong> <span data-key="approved_leave_requests">0</span></div>
                                <div class="col-md-6 col-12"><strong>Month:</strong> <span data-key="month">-</span> | <strong>Period:</strong> <span data-key="period">-</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right mt-3">Submit</button>

            </form>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userField = document.querySelector('select[name="user_id"]');
            const yearField = document.querySelector('select[name="year"]');
            const monthField = document.querySelector('select[name="report_month"]');
            const preview = document.getElementById('reportPreview');

            async function loadReportPreview() {
                const userId = userField.value;
                const year = yearField.value;
                const reportMonth = monthField.value;

                if (!userId || !year || !reportMonth) {
                    return;
                }

                const url = new URL('{{ route('admin.performance.report-data') }}', window.location.origin);
                url.searchParams.set('user_id', userId);
                url.searchParams.set('year', year);
                url.searchParams.set('report_month', reportMonth);

                try {
                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    preview.querySelector('[data-key="present_days"]').textContent = data.present_days ?? 0;
                    preview.querySelector('[data-key="late_days"]').textContent = data.late_days ?? 0;
                    preview.querySelector('[data-key="absent_days"]').textContent = data.absent_days ?? 0;
                    preview.querySelector('[data-key="leave_days"]').textContent = data.leave_days ?? 0;
                    preview.querySelector('[data-key="approved_leave_requests"]').textContent = data.approved_leave_requests ?? 0;
                    preview.querySelector('[data-key="month"]').textContent = data.report_month_name ?? '-';
                    preview.querySelector('[data-key="period"]').textContent = `${data.start_date ?? '-'} to ${data.end_date ?? '-'}`;
                } catch (error) {
                    // Silent fail so form flow is not interrupted.
                }
            }

            [userField, yearField, monthField].forEach(function (field) {
                field.addEventListener('change', loadReportPreview);
            });

            loadReportPreview();
        });
    </script>
@endsection
