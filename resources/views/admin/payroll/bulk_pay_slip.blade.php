@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Bulk Pay Slips') }}</title>
@endsection

@push('css')
<style>
    .bulk-slip { page-break-after: always; margin-bottom: 40px; }
    @media print {
        .no-print { display: none !important; }
        .bulk-slip { page-break-after: always; }
    }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area no-print">
    <h1>Bulk Pay Slips — {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</h1>
</div>

@include(adminTheme().'alerts')

<div class="no-print mb-3">
    <button onclick="window.print()" class="btn btn-primary"><i class="bx bx-printer"></i> Print All</button>
    <a href="{{ route('admin.payroll.salarySheet', ['month' => $month, 'year' => $year]) }}" class="btn btn-secondary">Back</a>
</div>

@forelse($salaries as $salarySheet)
    @php $employee = $salarySheet->user; $monthName = date('F Y', mktime(0,0,0,$salarySheet->month,1,$salarySheet->year)); @endphp
    <div class="card bulk-slip">
        <div class="card-body">
            <h4 class="text-center">{{ general()->title ?? 'Company' }} — Salary Slip</h4>
            <p class="text-center mb-3"><strong>{{ strtoupper($monthName) }}</strong></p>
            <table class="table table-bordered table-sm">
                <tr><th>Employee</th><td>{{ $employee->name }}</td><th>ID</th><td>{{ $employee->employee_id }}</td></tr>
                <tr><th>Department</th><td>{{ $employee->department->name ?? '-' }}</td><th>Net Pay</th><td><strong>৳{{ number_format($salarySheet->net_salary, 2) }}</strong></td></tr>
            </table>
            <div class="row">
                <div class="col-6">
                    <small>Earnings: ৳{{ number_format($salarySheet->total_earning, 2) }}</small>
                </div>
                <div class="col-6 text-end">
                    <small>Deductions: ৳{{ number_format($salarySheet->total_deduction, 2) }}</small>
                </div>
            </div>
            <p class="text-center mt-2 mb-0"><small>Tax ৳{{ number_format($salarySheet->tax, 2) }} | PF (Employee) ৳{{ number_format($salarySheet->provident_fund, 2) }}@if(($salarySheet->company_pf ?? 0) > 0) | PF (Employer) ৳{{ number_format($salarySheet->company_pf, 2) }}@endif</small></p>
        </div>
    </div>
@empty
    <div class="alert alert-warning">No salary sheets found for this period.</div>
@endforelse
@endsection
