@extends('admin.layouts.print')
@section('title')
<title>Salary Sheet Export</title>
@endsection

@section('contents')

<div class="print-area">
    <div class="text-center mb-4">
        <h4>Payroll Management System</h4>
        <h5>Monthly Salary Sheet - {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm" style="font-size: 12px;">
            <thead>
                <tr style="background: #f0f0f0;">
                    <th>SL</th>
                    <th>Emp ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Basic</th>
                    <th>House Rent</th>
                    <th>Medical</th>
                    <th>Transport</th>
                    <th>Other</th>
                    <th>Gross</th>
                    <th>Absent</th>
                    <th>Late</th>
                    <th>Tax/PF</th>
                    <th>Total Ded</th>
                    <th>Net Salary</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalBasic = 0;
                $totalHouse = 0;
                $totalMedical = 0;
                $totalTransport = 0;
                $totalOther = 0;
                $totalGross = 0;
                $totalAbsent = 0;
                $totalLate = 0;
                $totalTaxPf = 0;
                $totalDeduction = 0;
                $totalNet = 0;
                @endphp
                
                @forelse($salaries as $key => $salary)
                @php
                $totalBasic += $salary->basic_salary;
                $totalHouse += $salary->house_rent;
                $totalMedical += $salary->medical_allowance;
                $totalTransport += $salary->transport_allowance;
                $totalOther += $salary->other_allowance;
                $totalGross += $salary->gross_salary;
                $totalAbsent += $salary->absent_deduction;
                $totalLate += $salary->late_deduction;
                $totalTaxPf += $salary->tax_deduction;
                $totalDeduction += $salary->total_deduction;
                $totalNet += $salary->net_salary;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $salary->user->employee_id ?? 'N/A' }}</td>
                    <td>{{ $salary->user->name ?? 'N/A' }}</td>
                    <td>{{ $salary->user->employeeInfo->department->name ?? 'N/A' }}</td>
                    <td>{{ $salary->user->employeeInfo->designation->name ?? 'N/A' }}</td>
                    <td>{{ number_format($salary->basic_salary, 2) }}</td>
                    <td>{{ number_format($salary->house_rent, 2) }}</td>
                    <td>{{ number_format($salary->medical_allowance, 2) }}</td>
                    <td>{{ number_format($salary->transport_allowance, 2) }}</td>
                    <td>{{ number_format($salary->other_allowance, 2) }}</td>
                    <td>{{ number_format($salary->gross_salary, 2) }}</td>
                    <td>{{ number_format($salary->absent_deduction, 2) }}</td>
                    <td>{{ number_format($salary->late_deduction, 2) }}</td>
                    <td>{{ number_format($salary->tax_deduction, 2) }}</td>
                    <td>{{ number_format($salary->total_deduction, 2) }}</td>
                    <td><strong>{{ number_format($salary->net_salary, 2) }}</strong></td>
                    <td>
                        @if($salary->payment_status == 'paid')
                        <span class="badge bg-success">Paid</span>
                        @elseif($salary->payment_status == 'held')
                        <span class="badge bg-warning">Held</span>
                        @else
                        <span class="badge bg-danger">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="17" class="text-center">No salary records found for this month.</td>
                </tr>
                @endforelse
                
                @if($salaries->count() > 0)
                <tr style="background: #f0f0f0; font-weight: bold;">
                    <td colspan="5" class="text-right">Total</td>
                    <td>{{ number_format($totalBasic, 2) }}</td>
                    <td>{{ number_format($totalHouse, 2) }}</td>
                    <td>{{ number_format($totalMedical, 2) }}</td>
                    <td>{{ number_format($totalTransport, 2) }}</td>
                    <td>{{ number_format($totalOther, 2) }}</td>
                    <td>{{ number_format($totalGross, 2) }}</td>
                    <td>{{ number_format($totalAbsent, 2) }}</td>
                    <td>{{ number_format($totalLate, 2) }}</td>
                    <td>{{ number_format($totalTaxPf, 2) }}</td>
                    <td>{{ number_format($totalDeduction, 2) }}</td>
                    <td>{{ number_format($totalNet, 2) }}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        <p><strong>Generated on:</strong> {{ date('d-m-Y h:i A') }}</p>
    </div>
</div>

@endsection
