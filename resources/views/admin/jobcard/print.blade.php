@extends('printMasterBlank')
@section('title', 'Job Card Print')

@push('css')
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 9.5px; background: #fff; color: #111; }

    @media print {
        @page { size: A4; margin: 8mm; }
        body { font-size: 9px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .no-print { display: none !important; }
        .page-break { page-break-after: always; break-after: page; }
    }

    .jc-wrap {
        width: 100%;
        max-width: 210mm;
        margin: 0 auto;
        padding: 6px 0;
    }

    /* ---- Header ---- */
    .jc-header {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #1a3a5c 0%, #0d6efd 100%);
        color: #fff;
        padding: 8px 12px;
        border-radius: 4px 4px 0 0;
        margin-bottom: 0;
    }
    .jc-header .co-logo { width: 42px; height: 42px; object-fit: contain; background: #fff; border-radius: 3px; padding: 2px; }
    .jc-header .co-info { flex: 1; }
    .jc-header .co-name { font-size: 14px; font-weight: 700; }
    .jc-header .co-addr { font-size: 8px; opacity: .85; margin-top: 1px; }
    .jc-header .jc-title-box { text-align: right; }
    .jc-header .jc-title { font-size: 12px; font-weight: 700; letter-spacing: .5px; }
    .jc-header .jc-period { font-size: 9px; opacity: .9; margin-top: 2px; }
    .jc-header .jc-printed { font-size: 7.5px; opacity: .7; margin-top: 1px; }

    /* ---- Compliance Note ---- */
    .jc-compliance-bar {
        background: #fff8e1;
        border: 1px solid #f9a825;
        border-top: none;
        padding: 4px 10px;
        font-size: 7.5px;
        color: #5d4037;
    }
    .jc-compliance-bar strong { color: #e65100; }

    /* ---- Employee Info Grid ---- */
    .jc-emp {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        font-size: 8.5px;
    }
    .jc-emp td {
        padding: 4px 7px;
        border: 1px solid #bcd3ef;
    }
    .jc-emp td:nth-child(odd) {
        background: #eaf2ff;
        font-weight: 700;
        color: #1a3a5c;
        width: 18%;
    }
    .jc-emp td:nth-child(even) {
        color: #0d1f3c;
        width: 32%;
    }

    /* ---- Salary / OT Formula Box ---- */
    .jc-formula {
        display: flex;
        gap: 6px;
        margin: 6px 0;
    }
    .jc-formula-box {
        flex: 1;
        background: #f0f7ff;
        border: 1px solid #c8ddf7;
        border-radius: 3px;
        padding: 5px 8px;
        font-size: 8px;
    }
    .jc-formula-box .fbox-title {
        font-size: 7.5px;
        font-weight: 700;
        color: #1a3a5c;
        text-transform: uppercase;
        border-bottom: 1px solid #c8ddf7;
        margin-bottom: 4px;
        padding-bottom: 2px;
        letter-spacing: .3px;
    }
    .jc-formula-box .frow {
        display: flex;
        justify-content: space-between;
        padding: 1px 0;
        color: #3b5272;
    }
    .jc-formula-box .frow .fval { font-weight: 700; color: #0d1f3c; }
    .jc-formula-box .frow.hl .fval { color: #0d6efd; }
    .jc-formula-box .frow.eq {
        margin-top: 3px;
        border-top: 1px solid #c8ddf7;
        padding-top: 3px;
        font-weight: 700;
        font-size: 8.5px;
        color: #1a3a5c;
    }
    .jc-formula-box .frow.eq .fval { color: #e65100; font-size: 9px; }

    /* ---- Attendance Table ---- */
    .jc-att {
        width: 100%;
        border-collapse: collapse;
        margin: 6px 0;
        font-size: 8px;
    }
    .jc-att thead tr th {
        background: #1a3a5c;
        color: #fff;
        padding: 4px 5px;
        text-align: center;
        border: 1px solid #0d2d50;
        font-size: 7.5px;
        letter-spacing: .2px;
    }
    .jc-att tbody tr:nth-child(even) { background: #f6faff; }
    .jc-att tbody td {
        padding: 2.5px 4px;
        border: 1px solid #c8d8e8;
        text-align: center;
        font-size: 8px;
        color: #1a2a3a;
    }
    .jc-att tfoot td {
        padding: 4px 5px;
        border: 1px solid #c8d8e8;
        font-weight: 700;
        background: #eaf2ff;
        font-size: 8.5px;
    }

    /* Status badges */
    .st-P  { color: #155724; font-weight: 700; }
    .st-LT { color: #856404; font-weight: 700; }
    .st-A  { color: #721c24; font-weight: 700; }
    .st-L  { color: #004085; font-weight: 700; }
    .st-H  { color: #0c5460; font-weight: 700; }
    .st-WO { color: #5d2e8c; font-weight: 700; }

    /* ---- Summary Grid ---- */
    .jc-summary {
        width: 100%;
        border-collapse: collapse;
        margin: 6px 0;
        font-size: 8.5px;
    }
    .jc-summary td {
        padding: 4px 7px;
        border: 1px solid #bcd3ef;
    }
    .jc-summary td:nth-child(odd) {
        background: #eaf2ff;
        font-weight: 700;
        color: #1a3a5c;
        width: 22%;
    }
    .jc-summary td:nth-child(even) {
        color: #0d1f3c;
        width: 28%;
        font-weight: 700;
    }
    .jc-summary tr.ot-row td {
        background: #fff3cd;
    }
    .jc-summary tr.ot-row td:nth-child(odd) {
        background: #ffe082;
        color: #5d4037;
    }

    /* ---- Legend ---- */
    .jc-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 16px;
        background: #f8fafc;
        border: 1px solid #dce8f5;
        padding: 4px 8px;
        font-size: 7.5px;
        margin: 4px 0;
    }
    .jc-legend span { color: #3b5272; }
    .jc-legend b { color: #1a3a5c; }

    /* ---- Footer ---- */
    .jc-footer {
        background: #f6faff;
        border: 1px solid #bcd3ef;
        padding: 5px 10px;
        margin-top: 6px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        font-size: 7.5px;
        color: #5d7fa0;
    }
    .jc-footer .law { font-style: italic; flex: 1; }
    .jc-footer .sig-row { display: flex; gap: 30px; }
    .jc-footer .sig-box { text-align: center; }
    .jc-footer .sig-line { border-top: 1px solid #888; margin-top: 18px; padding-top: 2px; font-size: 7px; min-width: 80px; }
</style>
@endpush

@section('contents')
@foreach($employeesData as $data)
@php
    $employee  = $data['employee'];
    $summary   = $data['summary'];
    $dailyData = $data['dailyData'];
    $increments = $data['increments'];

    $salaryInfo   = $employee->salaryInfo();
    $gross        = $salaryInfo['gross_salary'];
    $mtf          = $salaryInfo['mtf'];
    $basicSalary  = $salaryInfo['basic_salary'];
    $houseRent    = $salaryInfo['house_rent'];
    $otRateCalc   = $salaryInfo['ot_rate'];

    $totalOt     = $summary['total_ot_hours'];
    $otRate      = $summary['ot_rate'];
    $totalOtAmt  = $summary['total_ot_amount'];

    $monthFormatted = \Carbon\Carbon::parse($month)->format('F Y');
    $daysInMonth = \Carbon\Carbon::parse($month)->daysInMonth;

    $statusLabel = [
        'P'  => 'Present',
        'LT' => 'Late',
        'A'  => 'Absent',
        'L'  => 'Leave',
        'H'  => 'Holiday',
        'WO' => 'Weekly Off',
    ];
@endphp

<div class="jc-wrap">

    {{-- Header --}}
    <div class="jc-header">
        @if(general() && general()->logo())
            <img src="{{ asset(general()->logo()) }}" alt="Logo" class="co-logo">
        @endif
        <div class="co-info">
            <div class="co-name">{{ general()->title ?? 'Company Name' }}</div>
            <div class="co-addr">{{ general()->address_one ?? '' }}</div>
        </div>
        <div class="jc-title-box">
            <div class="jc-title">JOB CARD (কার্ড)</div>
            <div class="jc-period">{{ $monthFormatted }}</div>
            <div class="jc-printed">Printed: {{ now()->format('d-M-Y H:i') }}</div>
        </div>
    </div>

    {{-- Bangladesh Labour Act compliance bar --}}
    <div class="jc-compliance-bar">
        <strong>Legal Reference:</strong>
        Bangladesh Labour Act 2006 (Act No. XLII of 2006) — §108: Max OT 2 hrs/day &amp; 12 hrs/week |
        §109: OT at <strong>double the basic wage rate</strong> | §100-110: Working hours &amp; rest intervals.
        Bangladesh Labour Rules 2015, Rule 101 — Attendance register must be maintained.
        Factory Act 1965, §36: Daily attendance record obligation.
    </div>

    {{-- Employee Info --}}
    <table class="jc-emp">
        <tr>
            <td>Employee ID</td>
            <td>{{ $employee->employee_id ?? $employee->id }}</td>
            <td>Department</td>
            <td>{{ $employee->department?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Name</td>
            <td>{{ $employee->name }}</td>
            <td>Section</td>
            <td>{{ $employee->section?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Designation</td>
            <td>{{ $employee->designation?->name ?? 'N/A' }}</td>
            <td>Shift</td>
            <td>{{ $employee->shift?->name_of_shift ?? $employee->shift?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Classification</td>
            <td>{{ $employee->employeeType?->name ?? $employee->employee_type ?? 'N/A' }}</td>
            <td>Join Date</td>
            <td>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d-M-Y') : 'N/A' }}</td>
        </tr>
    </table>

    {{-- Salary / OT Formula Boxes --}}
    <div class="jc-formula">
        <div class="jc-formula-box">
            <div class="fbox-title">Salary Structure (বেতন কাঠামো)</div>
            <div class="frow"><span>Gross Salary</span><span class="fval">{{ number_format($gross, 2) }}</span></div>
            <div class="frow"><span>MTF (Med+Trans+Food)</span><span class="fval">{{ number_format($mtf, 2) }}</span></div>
            <div class="frow hl"><span>Basic = (Gross−MTF) ÷ 1.5</span><span class="fval">{{ number_format($basicSalary, 2) }}</span></div>
            <div class="frow hl"><span>House Rent = Basic ÷ 2</span><span class="fval">{{ number_format($houseRent, 2) }}</span></div>
        </div>
        <div class="jc-formula-box">
            <div class="fbox-title">OT Formula [Labour Act §109]</div>
            <div class="frow"><span>Basic Salary / Month</span><span class="fval">{{ number_format($basicSalary, 2) }}</span></div>
            <div class="frow"><span>Monthly Hours (Standard)</span><span class="fval">208 hrs</span></div>
            <div class="frow"><span>OT Multiplier (Double Rate)</span><span class="fval">× 2</span></div>
            <div class="frow eq"><span>OT Rate = (Basic ÷ 208) × 2</span><span class="fval">{{ number_format($otRateCalc, 2) }} / hr</span></div>
        </div>
        <div class="jc-formula-box">
            <div class="fbox-title">OT Summary ({{ $monthFormatted }})</div>
            <div class="frow"><span>Total OT Hours</span><span class="fval">{{ number_format($totalOt, 2) }} hrs</span></div>
            <div class="frow"><span>OT Rate / Hour</span><span class="fval">{{ number_format($otRate, 2) }}</span></div>
            <div class="frow eq"><span>Total OT Amount</span><span class="fval">{{ number_format($totalOtAmt, 2) }}</span></div>
        </div>
    </div>

    {{-- Attendance Table --}}
    <table class="jc-att">
        <thead>
            <tr>
                <th style="width:4%">SL</th>
                <th style="width:10%">Date</th>
                <th style="width:8%">Day</th>
                <th style="width:13%">Shift</th>
                <th style="width:11%">In Time</th>
                <th style="width:11%">Out Time</th>
                <th style="width:8%">Wrk Hrs</th>
                <th style="width:8%">OT Hrs</th>
                <th style="width:7%">Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyData as $i => $row)
            @php
                $stCode = $row['status'];
                $stClass = 'st-' . $stCode;
                $stFull  = $statusLabel[$stCode] ?? $stCode;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($month)->format('Y-m-') . str_pad($row['date'], 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $row['day'] }}</td>
                <td>{{ $employee->shift?->name_of_shift ?? '-' }}</td>
                <td>{{ $row['in_time'] !== '-' ? $row['in_time'] : '—' }}</td>
                <td>{{ $row['out_time'] !== '-' ? $row['out_time'] : '—' }}</td>
                <td>{{ is_numeric($row['work_hours']) ? number_format($row['work_hours'], 2) : $row['work_hours'] }}</td>
                <td>{{ $row['ot_hours'] > 0 ? number_format($row['ot_hours'], 2) : '—' }}</td>
                <td class="{{ $stClass }}">{{ $stCode }}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align:right;">Total OT Hours:</td>
                <td style="color:#e65100;">{{ number_format($totalOt, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    {{-- Status Legend --}}
    <div class="jc-legend">
        <span><b class="st-P">P</b> = Present</span>
        <span><b class="st-LT">LT</b> = Late (বিলম্ব)</span>
        <span><b class="st-A">A</b> = Absent (অনুপস্থিত)</span>
        <span><b class="st-L">L</b> = Leave (ছুটি)</span>
        <span><b class="st-H">H</b> = Holiday (সরকারি ছুটি)</span>
        <span><b class="st-WO">WO</b> = Weekly Off (সাপ্তাহিক)</span>
    </div>

    {{-- Attendance Summary --}}
    <table class="jc-summary">
        <tr>
            <td>Total Days in Month</td>
            <td>{{ $daysInMonth }}</td>
            <td>Working Days (P+LT)</td>
            <td>{{ $summary['present'] + $summary['late'] }}</td>
        </tr>
        <tr>
            <td>Present Days (P)</td>
            <td>{{ $summary['present'] }}</td>
            <td>Late Days (LT)</td>
            <td>{{ $summary['late'] }}</td>
        </tr>
        <tr>
            <td>Absent Days (A)</td>
            <td style="color:#721c24; font-weight:700;">{{ $summary['absent'] }}</td>
            <td>Leave Days (L)</td>
            <td>{{ $summary['leave'] }}</td>
        </tr>
        <tr>
            <td>Holiday (H)</td>
            <td>{{ $summary['holiday'] }}</td>
            <td>Weekly Off (WO)</td>
            <td>{{ $summary['weekly_off'] }}</td>
        </tr>
        <tr>
            <td>Total Work Hours</td>
            <td>{{ number_format($summary['total_work_hours'], 2) }}</td>
            <td>Standard Hours/Month</td>
            <td>208 hrs [Labour Act §100]</td>
        </tr>
        <tr class="ot-row">
            <td>OT Rate/Hour [§109]</td>
            <td>{{ number_format($otRate, 2) }}</td>
            <td>Total OT Hours</td>
            <td>{{ number_format($totalOt, 2) }}</td>
        </tr>
        <tr class="ot-row">
            <td>Total OT Amount</td>
            <td colspan="3" style="color:#c62828; font-size:10px; font-weight:700;">
                BDT {{ number_format($totalOtAmt, 2) }}
                @php
                    $words = '';
                    try { $words = numberToWords(abs($totalOtAmt)); } catch(\Exception $e) {}
                @endphp
                @if($words)
                    — {{ ucfirst($words) }} Taka Only
                @endif
            </td>
        </tr>
    </table>

    {{-- Salary Increment History (if any) --}}
    @if($increments->count() > 0)
    <table style="width:100%; border-collapse:collapse; font-size:8px; margin-top:6px;">
        <thead>
            <tr style="background:#1a3a5c; color:#fff;">
                <th style="padding:3px 6px; border:1px solid #0d2d50;">#</th>
                <th style="padding:3px 6px; border:1px solid #0d2d50;">Increment Date</th>
                <th style="padding:3px 6px; border:1px solid #0d2d50;">Previous Salary</th>
                <th style="padding:3px 6px; border:1px solid #0d2d50;">Increment Amt</th>
                <th style="padding:3px 6px; border:1px solid #0d2d50;">New Salary</th>
                <th style="padding:3px 6px; border:1px solid #0d2d50;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($increments as $inc)
            <tr style="background:{{ $loop->even ? '#f6faff' : '#fff' }};">
                <td style="padding:2px 6px; border:1px solid #c8d8e8; text-align:center;">{{ $loop->iteration }}</td>
                <td style="padding:2px 6px; border:1px solid #c8d8e8; text-align:center;">{{ $inc->increment_date ? \Carbon\Carbon::parse($inc->increment_date)->format('d-M-Y') : '-' }}</td>
                <td style="padding:2px 6px; border:1px solid #c8d8e8; text-align:right;">{{ number_format($inc->previous_salary, 2) }}</td>
                <td style="padding:2px 6px; border:1px solid #c8d8e8; text-align:right;">{{ number_format($inc->increment_amount, 2) }}</td>
                <td style="padding:2px 6px; border:1px solid #c8d8e8; text-align:right; font-weight:700; color:#155724;">{{ number_format($inc->new_salary, 2) }}</td>
                <td style="padding:2px 6px; border:1px solid #c8d8e8;">{{ $inc->remarks ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Legal Footer with Signatures --}}
    <div class="jc-footer">
        <div class="law">
            <strong>Compliance:</strong>
            Bangladesh Labour Act 2006 §108: Maximum OT is 2 hours/day &amp; 12 hours/week.
            §109: OT must be paid at double the basic hourly rate. Formula: OT Rate = (Basic ÷ 208) × 2.
            §115: Workers must receive ≥1 hour rest after 5 hours of work.
            Bangladesh Labour Rules 2015, Rule 101 — This card serves as the official attendance record.
        </div>
        <div class="sig-row">
            <div class="sig-box">
                <div class="sig-line">HR Manager</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Accounts / Finance</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Worker Signature</div>
            </div>
        </div>
    </div>

</div>{{-- .jc-wrap --}}

@if(!$loop->last)
    <div class="page-break"></div>
@endif
@endforeach
@endsection
