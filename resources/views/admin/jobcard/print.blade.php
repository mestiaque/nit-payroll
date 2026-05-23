@extends('printMasterBlank')

@section('title', 'Job Card Print')

@push('css')
<style>
    @media print {
        body { font-size: 11px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @page { margin: 0.5cm; size: A4; }
    }
    .container {
        max-width: 210mm;
        background: #fff;
        padding: 10px;
    }
    .report-head {
        text-align: center;
        margin-bottom: 15px;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
    }
    .report-head h3 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: bold;
    }
    .report-head p {
        margin: 0;
        font-size: 10px;
    }
    .sub-title {
        text-align: center;
        font-weight: bold;
        margin: 15px 0;
        font-size: 13px;
    }
    .info-grid {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 10px;
    }
    .info-grid td {
        padding: 6px 8px;
        border: 1px solid #999;
    }
    .info-grid td:nth-child(odd) {
        background: #f9f9f9;
        font-weight: bold;
        width: 20%;
    }
    table.t {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    table.t thead th {
        background: #343a40;
        color: #fff;
        padding: 6px;
        text-align: center;
        font-weight: bold;
        border: 1px solid #999;
        font-size: 9px;
    }
    table.t tbody td {
        padding: 4px 6px;
        border: 1px solid #999;
        text-align: center;
        font-size: 9px;
    }
    table.t tfoot td {
        padding: 6px;
        border: 1px solid #999;
        font-weight: bold;
        background: #f9f9f9;
        font-size: 9px;
    }
    .text-right {
        text-align: right;
    }
    .text-center,
    .tc {
        text-align: center;
    }
    .page-break {
        page-break-after: always;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('contents')
    @foreach($employeesData as $data)
        @php
            $employee = $data['employee'];
            $summary = $data['summary'];
            $dailyData = $data['dailyData'];
            $increments = $data['increments'];
        @endphp

        <div class="print-header">
            <div class="company-info">
                @if(general() && general()->logo())
                <img src="{{ asset(general()->logo()) }}" alt="Logo" class="company-logo">
                @endif
                <div class="company-name">{{ general()->title ?? 'Company Name' }}</div>
                @if(general())
                    <div class="company-address">
                        {{ general()->address_one ?? '' }}
                    </div>
                    <div class="company-contact">
                        Phone: {{ general()->mobile ?? '' }}, Email: {{ general()->email ?? '' }}
                    </div>
                @endif
                <p></p>
                <div class="text-right" style="text-align: end; width: 42mm;">
                </div>
                <div class="report-title">
                    <span> Job Card ({{ \Carbon\Carbon::parse($month)->format('F Y') }})</span>
                </div>
                <span class="print-time"><i>{{ now()->format('d-m-Y H:i:s') }}</i></span>
            </div>
        </div>

        <table class="info-grid">
            <tr>
                <td>Employee ID</td><td>{{ $employee->employee_id ?? $employee->id }}</td>
                <td>Department</td><td>{{ $employee->department ? $employee->department->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Name</td><td>{{ $employee->name }}</td>
                <td>Section</td><td>{{ $employee->section ? $employee->section->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Classification</td><td>{{ $employee->employee_type ?? 'N/A' }}</td>
                <td>Designation</td><td>{{ $employee->designation ? $employee->designation->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Join Date</td>
                <td>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d-M-y') : 'N/A' }}</td>
                <td></td><td></td>
            </tr>
        </table>

        <table class="t">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Date</th>
                    <th>Shift</th>
                    <th>Day</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                    <th>OT Hrs</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyData as $index => $day)
                <tr>
                    <td class="tc">{{ $index + 1 }}</td>
                    <td class="tc">{{ $day['date'] }}</td>
                    <td class="tc">{{ $employee->shift ? $employee->shift->name : '-' }}</td>
                    <td class="tc">{{ $day['day'] }}</td>
                    <td class="tc">{{ $day['in_time'] }}</td>
                    <td class="tc">{{ $day['out_time'] }}</td>
                    <td class="tc">{{ number_format($day['ot_hours'] ?? 0, 2) }}</td>
                    <td class="tc">
                        @if($day['status'] == 'P')
                            P
                        @elseif($day['status'] == 'LT')
                            LT
                        @elseif($day['status'] == 'A')
                            A
                        @elseif($day['status'] == 'L')
                            L
                        @elseif($day['status'] == 'H')
                            H
                        @elseif($day['status'] == 'WO')
                            WO
                        @endif
                    </td>
                    <td class="tc">-</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><b>Total OT Hrs:</b></td>
                    <td class="text-center"><b>{{ number_format($summary['total_ot_hours'] ?? 0, 2) }}</b></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <table class="info-grid">
            <tr>
                <td>Total Days in Month</td>
                <td>{{ count($dailyData) }}</td>
                <td>Working Days</td>
                <td>{{ $summary['present'] + $summary['late'] }}</td>
            </tr>
            <tr>
                <td>Govt. Holidays</td>
                <td>{{ $summary['holiday'] }}</td>
                <td>Weekend Days</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Absent Days</td>
                <td>{{ $summary['absent'] }}</td>
                <td>Leave Days</td>
                <td>{{ $summary['leave'] }}</td>
            </tr>
            <tr>
                <td>Present Days</td>
                <td>{{ $summary['present'] }}</td>
                <td>Total Attendance</td>
                <td>{{ $summary['present'] + $summary['late'] }}</td>
            </tr>
            <tr>
                <td>Late</td>
                <td>{{ $summary['late'] }}</td>
                <td>Punch Missing</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Early Out</td>
                <td>0</td>
                <td>OT Rate / Hour</td>
                <td>{{ number_format($summary['ot_rate'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Total OT Amount</td>
                <td>{{ number_format($summary['total_ot_amount'] ?? 0, 2) }}</td>
                <td>Late & Early Out</td>
                <td>0</td>
            </tr>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
@endsection
