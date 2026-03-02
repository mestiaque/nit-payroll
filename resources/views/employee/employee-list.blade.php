@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Employee List</h3>
        <div>
            <a href="{{ route('employee.export') }}" class="btn btn-success">Export to Excel</a>
            <button class="btn btn-primary" onclick="window.print()">Print</button>
        </div>
    </div>
    <div class="a4-sheet">
        <div class="row align-items-center mb-3">
            <div class="col-2 text-center">
                <img src="/path/to/logo.png" alt="Company Logo" class="company-logo">
            </div>
            <div class="col-10">
                <h3 class="mb-0">Company Name</h3>
                <p class="mb-0">Company Address Line 1<br>Company Address Line 2</p>
            </div>
        </div>
        <hr>
        <div class="text-center mb-4">
            <h4>Employee List</h4>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->department->name ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row mt-5">
            <div class="col-4 signature-box">
                <span>Prepared By</span>
            </div>
            <div class="col-4 signature-box">
                <span>Checked By</span>
            </div>
            <div class="col-4 signature-box">
                <span>Approved By</span>
            </div>
        </div>
    </div>
</div>
<style>
    @media print {
        .btn, .d-flex, nav, .navbar, .print-btn { display: none !important; }
        .a4-sheet { box-shadow: none !important; border: none !important; }
        body, html { width: 210mm; height: 297mm; margin: 0; padding: 0; }
    }
    .a4-sheet { width: 210mm; min-height: 297mm; margin: 20px auto; background: #fff; box-shadow: 0 0 5px rgba(0,0,0,0.1); padding: 32px 24px; border-radius: 8px; border: 1px solid #eee; }
    .company-logo { max-height: 60px; }
    .signature-box { min-height: 60px; border-top: 1px solid #333; margin-top: 32px; text-align: center; }
</style>
@endsection
