@php
    $copies = ['Office Copy', 'Employee Copy'];
@endphp

<div class="payslip-container">
    @foreach($copies as $copy)
        <div class="payslip-half">
            <div class="copy-type">{{ $copy }}</div>
            <div class="header">
                <h2>{{ $employeeData['company_name'] ?? '' }}</h2>
                <p>{{ $employeeData['company_address'] ?? '' }}</p>
                <p>Pay Slip Month: {{ $monthLabel ?? '' }}</p>
            </div>

            <table class="employee-info">
                <tr>
                    <td><strong>Employee ID:</strong> {{ $employeeData['employee_id'] ?? ($employee->employee_id ?: $employee->id) }}</td>
                    <td><strong>Department:</strong> {{ $employeeData['department'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Name:</strong> {{ $employeeData['employee_name'] ?? $employee->name }}</td>
                    <td><strong>Section:</strong> {{ $employeeData['section'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Classification:</strong> {{ $employeeData['employee_type'] ?? '-' }}</td>
                    <td><strong>Designation:</strong> {{ $employeeData['designation'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Join Date:</strong> {{ $employeeData['joining_date'] ?? '-' }}</td>
                    <td><strong>Shift:</strong> {{ $employeeData['shift'] ?? '-' }}</td>
                </tr>
            </table>

            <table class="salary-table">
                <tr>
                    <td class="label">Basic Salary:</td>
                    <td class="value">{{ number_format($salary['basic'] ?? 0, 2) }}</td>
                    <td class="label">Attendance Bonus:</td>
                    <td class="value">{{ number_format($attendanceBonus, 2) }}</td>
                    <td class="label right-align">Total Days:</td>
                    <td class="value right-align">{{ number_format($totalDays, 0) }}</td>
                </tr>
                <tr>
                    <td class="label">House Rent:</td>
                    <td class="value">{{ number_format($salary['house'] ?? 0, 2) }}</td>
                    <td class="label">Medical Allowance:</td>
                    <td class="value">{{ number_format($salary['medical'] ?? 0, 2) }}</td>
                    <td class="label right-align">Present Days:</td>
                    <td class="value right-align">{{ number_format($present, 0) }}</td>
                </tr>
                <tr>
                    <td class="label">Transport Allowance:</td>
                    <td class="value">{{ number_format($salary['transport'] ?? 0, 2) }}</td>
                    <td class="label">Food Allowance:</td>
                    <td class="value">{{ number_format($salary['food'] ?? 0, 2) }}</td>
                    <td class="label right-align">Absent Days:</td>
                    <td class="value right-align">{{ number_format($absent, 0) }}</td>
                </tr>
                <tr>
                    <td class="label bottom-border">Gross Salary:</td>
                    <td class="value bottom-border">{{ number_format($totalSalary, 2) }}</td>
                    <td class="label">OT Rate:</td>
                    <td class="value">{{ number_format($otRate, 2) }}</td>
                    <td class="label right-align">Casual Leave:</td>
                    <td class="value right-align">{{ number_format($casual, 0) }}</td>
                </tr>
                <tr>
                    <td class="label">OT Amount:</td>
                    <td class="value">{{ number_format($otAmount, 2) }}</td>
                    <td class="label">OT Hours:</td>
                    <td class="value">{{ number_format($otHour, 2) }}</td>
                    <td class="label right-align">Sick Leave:</td>
                    <td class="value right-align">{{ number_format($sick, 0) }}</td>
                </tr>
                <tr>
                    <td class="label bottom-border">Total Earnings:</td>
                    <td class="value bottom-border">{{ number_format($payable, 2) }}</td>
                    <td class="label">Other Facilities:</td>
                    <td class="value">{{ number_format($phoneInternet + $extraFacility + $carFuel, 2) }}</td>
                    <td class="label right-align">Earned Leave:</td>
                    <td class="value right-align">{{ number_format($earned, 0) }}</td>
                </tr>
                <tr>
                    <td class="label">Total Deductions:</td>
                    <td class="value">{{ number_format($totalDeductions, 2) }}</td>
                    <td class="label"></td>
                    <td class="value"></td>
                    <td class="label right-align">Weekly Off:</td>
                    <td class="value right-align">{{ number_format($weekly, 0) }}</td>
                </tr>
                <tr>
                    <td class="label bottom-border">Advance Deduction:</td>
                    <td class="value bottom-border">{{ number_format($advance, 2) }}</td>
                    <td class="label"></td>
                    <td class="value"></td>
                    <td class="label right-align">Festival Leave:</td>
                    <td class="value right-align">{{ number_format($festival, 0) }}</td>
                </tr>
                <tr>
                    <td class="label">Net Pay:</td>
                    <td class="value">{{ number_format($payable - ($advance + $totalDeductions), 2) }}</td>
                    <td class="label"></td>
                    <td class="value"></td>
                    <td class="label right-align">General Leave:</td>
                    <td class="value right-align">{{ number_format($general, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td class="label right-align">Maternity Leave:</td>
                    <td class="value right-align">{{ number_format($maternity, 0) }}</td>
                </tr>
            </table>

            <div class="footer">
                For any complaint or suggestion, please contact the Human Resources and Compliance Department.
            </div>
            <div class="signature">Signature</div>
        </div>

        @if(!$loop->last)
            <div class="dashed-line"></div>
        @endif
    @endforeach
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        margin: 0;
    }

    .payslip-container {
        width: 800px;
        margin: 0 auto;
        background-color: #fff;
        padding: 8px 5px;
        border: 1px dashed #000;
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .payslip-half {
        width: 48%;
        position: relative;
    }

    .header {
        text-align: center;
        margin-bottom: 4px;
    }

    .header h2 {
        margin: 0;
        font-size: 14px;
    }

    .header p {
        margin: 0;
        font-size: 9px;
    }

    .copy-type {
        text-align: right;
        font-weight: bold;
        font-size: 12px;
        position: absolute;
        top: 0.2rem;
        right: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .employee-info {
        margin-bottom: 2px;
        border-bottom: 1px solid #666;
    }

    .employee-info td {
        font-size: 8px !important;
        padding: 1px 0;
        border: none !important;
    }

    .salary-table td {
        padding: 1px 0;
        vertical-align: top;
        font-size: 8px !important;
        border: none !important;
    }

    .label {
        font-weight: bold;
        width: 92px;
    }

    .value {
        text-align: left;
        color: #000;
        font-weight: bold;
    }

    .right-align {
        text-align: right;
    }

    .bottom-border {
        border-bottom: 1px solid #777 !important;
    }

    .footer {
        margin-top: 2px;
        font-size: 9px;
    }

    .signature {
        margin-top: 18px;
        text-align: right;
        border-top: 1px solid #000;
        width: 80px;
        float: right;
    }

    .dashed-line {
        border-left: 1px dashed #000;
        height: auto;
    }
</style>
