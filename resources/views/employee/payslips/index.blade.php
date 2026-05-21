@extends(employeeTheme().'layouts.app')
@section('title')
<title>My Payslips</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>My Payslips</h1>
</div>

@include(employeeTheme().'alerts')

<div class="card">
    <div class="card-body">
        <form method="get" class="row mb-3">
            <div class="col-md-3">
                <select name="year" class="form-control" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Gross</th>
                    <th>Deduction</th>
                    <th>Net Salary</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payslips as $sheet)
                <tr>
                    <td>{{ date('F', mktime(0,0,0,$sheet->month,1)) }} {{ $sheet->year }}</td>
                    <td>৳{{ number_format($sheet->gross_salary, 2) }}</td>
                    <td>৳{{ number_format($sheet->total_deduction, 2) }}</td>
                    <td><strong>৳{{ number_format($sheet->net_salary, 2) }}</strong></td>
                    <td><span class="badge bg-{{ $sheet->payment_status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($sheet->payment_status) }}</span></td>
                    <td>
                        <a href="{{ route('customer.payslips.show', $sheet->id) }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No payslips for {{ $year }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
