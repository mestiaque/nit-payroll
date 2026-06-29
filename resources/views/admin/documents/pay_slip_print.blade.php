{{-- Professional Pay Slip | Bangladesh Labour Act 2006, Section 123 & Labour Rules 2015, Rule 113 --}}
@php
    $netPay = $payable - ($advance + $totalDeductions);
    // Salary formula display
    $mtf = $medical + $transport + $food;
    $formulaBasic = $grossSalary > $mtf ? round(($grossSalary - $mtf) / 1.5, 2) : $basicSalary;
    $formulaHouseRent = round($formulaBasic / 2, 2);
    $formulaOtRate = round(($formulaBasic / 208) * 2, 2);
    $copies = [
        ['label' => 'Office Copy', 'bg' => '#f0f7ff'],
        ['label' => 'Employee Copy', 'bg' => '#f9fffe'],
    ];
    $companyName = $employeeData['company_name'] ?? 'Company Name';
    $companyAddress = $employeeData['company_address'] ?? '';
@endphp

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 9.5px; background: #fff; }

    .ps-page {
        width: 210mm;
        margin: 0 auto;
        padding: 6mm 6mm;
        display: flex;
        flex-direction: column;
        gap: 6mm;
    }

    .ps-copy {
        border: 1.5px solid #1a3a5c;
        border-radius: 4px;
        overflow: hidden;
        page-break-inside: avoid;
    }

    /* Header */
    .ps-header {
        background: linear-gradient(135deg, #1a3a5c 0%, #0d6efd 100%);
        color: #fff;
        padding: 6px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ps-header-left h1 { font-size: 13px; font-weight: 700; letter-spacing: .3px; }
    .ps-header-left p { font-size: 8px; opacity: .85; margin-top: 1px; }
    .ps-header-right { text-align: right; }
    .ps-header-right .copy-label {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 3px;
        padding: 2px 8px;
        font-size: 8.5px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
    }
    .ps-header-right .month-label { font-size: 10px; font-weight: 700; margin-bottom: 3px; }

    /* Employee Info Strip */
    .ps-emp-strip {
        background: #eaf2ff;
        border-bottom: 1px solid #c8ddf7;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
    }
    .ps-emp-cell {
        padding: 4px 8px;
        border-right: 1px solid #c8ddf7;
        font-size: 8px;
    }
    .ps-emp-cell:last-child { border-right: none; }
    .ps-emp-cell .lbl { color: #5d7fa0; font-weight: 600; font-size: 7.5px; text-transform: uppercase; }
    .ps-emp-cell .val { color: #0d1f3c; font-weight: 700; font-size: 9px; margin-top: 1px; }

    /* Body */
    .ps-body {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0;
    }

    /* Columns */
    .ps-col {
        padding: 5px 7px;
        border-right: 1px solid #dce8f5;
    }
    .ps-col:last-child { border-right: none; }

    .ps-col-title {
        font-size: 8px;
        font-weight: 700;
        color: #fff;
        background: #1a3a5c;
        padding: 3px 5px;
        margin: -5px -7px 5px -7px;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .ps-row {
        display: flex;
        justify-content: space-between;
        padding: 1.5px 0;
        border-bottom: 1px dotted #e2ecf7;
        font-size: 8.5px;
    }
    .ps-row:last-child { border-bottom: none; }
    .ps-row .lbl { color: #3b5272; }
    .ps-row .val { font-weight: 700; color: #0d1f3c; text-align: right; }
    .ps-row.total {
        border-top: 1.5px solid #1a3a5c;
        border-bottom: none;
        margin-top: 3px;
        padding-top: 3px;
        font-weight: 700;
        font-size: 9px;
    }
    .ps-row.total .lbl { color: #1a3a5c; }
    .ps-row.total .val { color: #0d6efd; }
    .ps-row.deduction .val { color: #c0392b; }
    .ps-row.formula {
        background: #f0f7ff;
        border-radius: 2px;
        padding: 2px 3px;
        border-bottom: 1px dotted #c8ddf7;
        font-size: 7.5px;
    }
    .ps-row.formula .lbl { color: #5b7fa8; font-style: italic; }
    .ps-row.formula .val { color: #1a3a5c; }

    /* Net Pay Banner */
    .ps-netpay {
        background: linear-gradient(90deg, #1a3a5c 0%, #0d6efd 100%);
        color: #fff;
        padding: 5px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ps-netpay .netpay-label { font-size: 9px; font-weight: 700; }
    .ps-netpay .netpay-amount { font-size: 13px; font-weight: 700; letter-spacing: .3px; }
    .ps-netpay .netpay-words { font-size: 7.5px; opacity: .85; margin-top: 1px; }

    /* Attendance Summary */
    .ps-attendance {
        background: #f6faff;
        border-top: 1px solid #dce8f5;
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 0;
    }
    .ps-att-cell {
        padding: 3px 5px;
        text-align: center;
        border-right: 1px solid #dce8f5;
        font-size: 7.5px;
    }
    .ps-att-cell:last-child { border-right: none; }
    .ps-att-cell .att-lbl { color: #5d7fa0; font-size: 7px; }
    .ps-att-cell .att-val { font-weight: 700; font-size: 10px; color: #0d1f3c; }

    /* Footer */
    .ps-footer {
        background: #f8fafc;
        border-top: 1px solid #dce8f5;
        padding: 4px 10px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 7.5px;
        color: #5d7fa0;
    }
    .ps-footer .law-ref { font-style: italic; }
    .ps-footer .sig-area { text-align: center; min-width: 100px; }
    .ps-footer .sig-line { border-top: 1px solid #888; margin-top: 14px; padding-top: 2px; font-size: 7px; }

    .divider {
        border: none;
        border-top: 2px dashed #aac4e6;
        margin: 3mm 0;
    }

    @media print {
        @page { size: A4; margin: 6mm; }
        body { margin: 0; }
        .ps-page { padding: 0; gap: 4mm; }
        .divider { border-top: 2px dashed #999; }
    }
</style>

<div class="ps-page">
@foreach($copies as $idx => $copy)
    <div class="ps-copy">

        {{-- Header --}}
        <div class="ps-header">
            <div class="ps-header-left">
                <h1>{{ $companyName }}</h1>
                <p>{{ $companyAddress }}</p>
            </div>
            <div class="ps-header-right">
                <div class="month-label">SALARY SLIP — {{ strtoupper($monthLabel ?? '') }}</div>
                <div class="copy-label">{{ $copy['label'] }}</div>
            </div>
        </div>

        {{-- Employee Strip --}}
        <div class="ps-emp-strip">
            <div class="ps-emp-cell">
                <div class="lbl">Employee ID</div>
                <div class="val">{{ $employeeData['employee_id'] ?? '-' }}</div>
            </div>
            <div class="ps-emp-cell">
                <div class="lbl">Name</div>
                <div class="val">{{ $employeeData['employee_name'] ?? '-' }}</div>
            </div>
            <div class="ps-emp-cell">
                <div class="lbl">Designation</div>
                <div class="val">{{ $employeeData['designation'] ?? '-' }}</div>
            </div>
            <div class="ps-emp-cell">
                <div class="lbl">Department / Section</div>
                <div class="val">{{ $employeeData['department'] ?? '-' }} / {{ $employeeData['section'] ?? '-' }}</div>
            </div>
            <div class="ps-emp-cell">
                <div class="lbl">Classification</div>
                <div class="val">{{ $employeeData['employee_type'] ?? '-' }}</div>
            </div>
            <div class="ps-emp-cell">
                <div class="lbl">Shift</div>
                <div class="val">{{ $employeeData['shift'] ?? '-' }}</div>
            </div>
            <div class="ps-emp-cell">
                <div class="lbl">Join Date</div>
                <div class="val">{{ $employeeData['joining_date'] ?? '-' }}</div>
            </div>
            <div class="ps-emp-cell">
                <div class="lbl">Payment Month</div>
                <div class="val">{{ $monthLabel ?? '-' }}</div>
            </div>
        </div>

        {{-- 3-column body --}}
        <div class="ps-body">

            {{-- Column 1: Earnings --}}
            <div class="ps-col">
                <div class="ps-col-title">Earnings</div>

                {{-- Formula reference (shows formula, not just values) --}}
                <div class="ps-row formula">
                    <span class="lbl">Gross Salary</span>
                    <span class="val">{{ number_format($grossSalary, 2) }}</span>
                </div>
                <div class="ps-row formula">
                    <span class="lbl">MTF (Med+Trans+Food)</span>
                    <span class="val">{{ number_format($mtf, 2) }}</span>
                </div>
                <div class="ps-row formula">
                    <span class="lbl">Basic = (Gross−MTF)÷1.5</span>
                    <span class="val">{{ number_format($basicSalary, 2) }}</span>
                </div>
                <div class="ps-row formula">
                    <span class="lbl">House Rent = Basic÷2</span>
                    <span class="val">{{ number_format($houseRent, 2) }}</span>
                </div>

                <div class="ps-row">
                    <span class="lbl">Medical Allowance</span>
                    <span class="val">{{ number_format($medical, 2) }}</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">Transport Allowance</span>
                    <span class="val">{{ number_format($transport, 2) }}</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">Food Allowance</span>
                    <span class="val">{{ number_format($food, 2) }}</span>
                </div>
                @if(($conveyance ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Conveyance</span>
                    <span class="val">{{ number_format($conveyance, 2) }}</span>
                </div>
                @endif
                @if(($otherAllowance ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Other Allowance</span>
                    <span class="val">{{ number_format($otherAllowance, 2) }}</span>
                </div>
                @endif
                @if(($attendanceBonus ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Attendance Bonus</span>
                    <span class="val">{{ number_format($attendanceBonus, 2) }}</span>
                </div>
                @endif
                @if(($bonus ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Festival Bonus</span>
                    <span class="val">{{ number_format($bonus, 2) }}</span>
                </div>
                @endif
                @if(($otherBonus ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Other Bonus</span>
                    <span class="val">{{ number_format($otherBonus, 2) }}</span>
                </div>
                @endif
                @if(($phoneInternet ?? 0) + ($extraFacility ?? 0) + ($carFuel ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Other Facilities</span>
                    <span class="val">{{ number_format(($phoneInternet ?? 0) + ($extraFacility ?? 0) + ($carFuel ?? 0), 2) }}</span>
                </div>
                @endif

                <div class="ps-row total">
                    <span class="lbl">Total Earnings</span>
                    <span class="val">{{ number_format($totalEarnings, 2) }}</span>
                </div>
            </div>

            {{-- Column 2: Overtime & Deductions --}}
            <div class="ps-col">
                <div class="ps-col-title">Overtime &amp; Deductions</div>

                {{-- OT Section [Labour Act 2006, §108-109] --}}
                <div class="ps-row formula">
                    <span class="lbl">OT Rate = (Basic÷208)×2</span>
                    <span class="val">{{ number_format($otRate, 2) }}/hr</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">OT Hours</span>
                    <span class="val">{{ number_format($otHour, 2) }} hrs</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">OT Amount</span>
                    <span class="val">{{ number_format($otAmount, 2) }}</span>
                </div>
                @if(($specialOvertime ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Special OT</span>
                    <span class="val">{{ number_format($specialOvertime, 2) }}</span>
                </div>
                @endif
                @if(($grassTime ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Grass Time</span>
                    <span class="val">{{ number_format($grassTime, 2) }}</span>
                </div>
                @endif

                <div style="border-top:1px solid #dce8f5; margin: 5px 0;"></div>

                {{-- Deductions --}}
                @if(($absentDeduction ?? 0) > 0)
                <div class="ps-row deduction">
                    <span class="lbl">Absent Deduction</span>
                    <span class="val">−{{ number_format($absentDeduction, 2) }}</span>
                </div>
                @endif
                @if(($lateDeduction ?? 0) > 0)
                <div class="ps-row deduction">
                    <span class="lbl">Late Deduction</span>
                    <span class="val">−{{ number_format($lateDeduction, 2) }}</span>
                </div>
                @endif
                @if(($taxDeduction ?? 0) > 0)
                <div class="ps-row deduction">
                    <span class="lbl">Income Tax</span>
                    <span class="val">−{{ number_format($taxDeduction, 2) }}</span>
                </div>
                @endif
                @if(($pfDeduction ?? 0) > 0)
                <div class="ps-row deduction">
                    <span class="lbl">Provident Fund</span>
                    <span class="val">−{{ number_format($pfDeduction, 2) }}</span>
                </div>
                @endif
                @if(($loanDeduction ?? 0) > 0)
                <div class="ps-row deduction">
                    <span class="lbl">Loan Instalment</span>
                    <span class="val">−{{ number_format($loanDeduction, 2) }}</span>
                </div>
                @endif
                @if(($advance ?? 0) > 0)
                <div class="ps-row deduction">
                    <span class="lbl">Salary Advance</span>
                    <span class="val">−{{ number_format($advance, 2) }}</span>
                </div>
                @endif
                @if(($otherDeductions ?? 0) > 0)
                <div class="ps-row deduction">
                    <span class="lbl">Other Deductions</span>
                    <span class="val">−{{ number_format($otherDeductions, 2) }}</span>
                </div>
                @endif
                @if(($stampCharge ?? 0) > 0)
                <div class="ps-row deduction">
                    <span class="lbl">Stamp Charge</span>
                    <span class="val">−{{ number_format($stampCharge, 2) }}</span>
                </div>
                @endif

                <div class="ps-row total deduction">
                    <span class="lbl">Total Deductions</span>
                    <span class="val">−{{ number_format($totalDeductions + $advance, 2) }}</span>
                </div>
            </div>

            {{-- Column 3: Attendance Summary --}}
            <div class="ps-col">
                <div class="ps-col-title">Attendance Summary</div>

                <div class="ps-row">
                    <span class="lbl">Total Days in Month</span>
                    <span class="val">{{ number_format($totalDays, 0) }}</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">Working Days</span>
                    <span class="val">{{ number_format($workingDays, 0) }}</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">Present Days</span>
                    <span class="val">{{ number_format($present, 0) }}</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">Absent Days</span>
                    <span class="val" style="color:#c0392b;">{{ number_format($absent, 0) }}</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">Late Days</span>
                    <span class="val" style="color:#e67e22;">{{ number_format($lateDays, 0) }}</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">Weekly Off</span>
                    <span class="val">{{ number_format($weekly, 0) }}</span>
                </div>
                <div class="ps-row">
                    <span class="lbl">Holiday Days</span>
                    <span class="val">{{ number_format($holidayDays, 0) }}</span>
                </div>
                @if(($casual ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Casual Leave</span>
                    <span class="val">{{ number_format($casual, 0) }}</span>
                </div>
                @endif
                @if(($sick ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Sick Leave</span>
                    <span class="val">{{ number_format($sick, 0) }}</span>
                </div>
                @endif
                @if(($earned ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Earned Leave</span>
                    <span class="val">{{ number_format($earned, 0) }}</span>
                </div>
                @endif
                @if(($festival ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Festival Leave</span>
                    <span class="val">{{ number_format($festival, 0) }}</span>
                </div>
                @endif
                @if(($general ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">General Leave</span>
                    <span class="val">{{ number_format($general, 0) }}</span>
                </div>
                @endif
                @if(($maternity ?? 0) > 0)
                <div class="ps-row">
                    <span class="lbl">Maternity Leave</span>
                    <span class="val">{{ number_format($maternity, 0) }}</span>
                </div>
                @endif

                {{-- Payment Status --}}
                @if($salarySheet)
                <div style="margin-top:6px; border-top:1px solid #dce8f5; padding-top:4px;">
                    <div class="ps-row">
                        <span class="lbl">Payment Status</span>
                        <span class="val" style="color:{{ $salarySheet->payment_status === 'paid' ? '#27ae60' : '#e67e22' }}; text-transform:uppercase; font-size:8px;">
                            {{ $salarySheet->payment_status ?? 'Pending' }}
                        </span>
                    </div>
                    @if($salarySheet->payment_method)
                    <div class="ps-row">
                        <span class="lbl">Payment Method</span>
                        <span class="val" style="text-transform:capitalize;">{{ str_replace('_', ' ', $salarySheet->payment_method) }}</span>
                    </div>
                    @endif
                    @if($salarySheet->payment_date)
                    <div class="ps-row">
                        <span class="lbl">Payment Date</span>
                        <span class="val">{{ \Carbon\Carbon::parse($salarySheet->payment_date)->format('d-M-Y') }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Net Pay Banner --}}
        <div class="ps-netpay">
            <div>
                <div class="netpay-label">NET PAY (টাকায় মোট)</div>
                @php
                    $netWords = '';
                    try { $netWords = numberToWords(abs($netPay)); } catch(\Exception $e) {}
                @endphp
                @if($netWords)
                <div class="netpay-words">{{ ucfirst($netWords) }} Taka Only</div>
                @endif
            </div>
            <div class="netpay-amount">BDT {{ number_format($netPay, 2) }}</div>
        </div>

        {{-- Legal Footer --}}
        <div class="ps-footer">
            <div class="law-ref">
                <strong>Legal Reference:</strong>
                Bangladesh Labour Act 2006 (Act No. XLII of 2006), §123 – Employer to give payslip &amp; §108–109 – Overtime at double rate (max 2 hrs/day, 12 hrs/week).
                Bangladesh Labour Rules 2015, Rule 113. Income Tax Ordinance 1984.
                <br>For complaints contact Human Resources &amp; Compliance Department.
            </div>
            <div style="display:flex; gap:30px;">
                <div class="sig-area">
                    <div class="sig-line">HR Manager</div>
                </div>
                <div class="sig-area">
                    <div class="sig-line">Account / Finance</div>
                </div>
                <div class="sig-area">
                    <div class="sig-line">Employee Signature</div>
                </div>
            </div>
        </div>

    </div>

    @if(!$loop->last)
        <hr class="divider">
    @endif
@endforeach
</div>
