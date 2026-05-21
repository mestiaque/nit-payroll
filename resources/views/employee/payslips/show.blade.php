@extends(employeeTheme().'layouts.app')
@section('title')
<title>Pay Slip</title>
@endsection

@section('contents')
<div class="breadcrumb-area">
    <h1>Pay Slip</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('customer.payslips.index') }}">Payslips</a></li>
        <li class="item">View</li>
    </ol>
</div>

<div class="mb-3">
    <a href="{{ route('customer.payslips.index') }}" class="btn btn-secondary">Back</a>
    <button onclick="window.print()" class="btn btn-primary">Print</button>
</div>

@include(adminTheme().'payroll.pay_slip', ['salarySheet' => $salarySheet, 'employeePortal' => true])

@endsection
