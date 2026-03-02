@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Monthly Salary Sheet') }}</title>
@endsection

@push('css')
<style>
    .salary-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
    table.table thead { background: #6c5ce7; color: white; }
    table.table { font-size: 13px; }
    .stat-box { padding: 15px; border-radius: 8px; text-align: center; }
</style>
@endpush



@section('contents')

<div class="breadcrumb-area">
    <h1>Monthly Salary Sheet</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Payroll</li>
        <li class="item">Salary Sheet</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

<div class="row mb-3">
    {{-- Employees --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #007bff38">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                     style="width:50px;height:50px;">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <h4 class="mb-0">{{ $summary['total_employees'] ?? 0 }}</h4>
                    <small class="text-muted">Employees</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Gross Salary --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #17a2b838">
            <div class="card-body d-flex align-items-center">
                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                     style="width:50px;height:50px;">
                    <i class="fa fa-money-bill-wave"></i>
                </div>
                <div>
                    <h5 class="mb-0">৳{{ number_format($summary['total_gross'] ?? 0, 0) }}</h5>
                    <small class="text-muted">Gross Salary</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Deduction --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #dc354538">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                     style="width:50px;height:50px;">
                    <i class="fa fa-minus-circle"></i>
                </div>
                <div>
                    <h5 class="mb-0">৳{{ number_format($summary['total_deduction'] ?? 0, 0) }}</h5>
                    <small class="text-muted">Total Deduction</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Net Salary --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0" style="background: #28a74538">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                     style="width:50px;height:50px;">
                    <i class="fa fa-wallet"></i>
                </div>
                <div>
                    <h4 class="mb-0">৳{{ number_format($summary['total_net'] ?? 0, 0) }}</h4>
                    <small class="text-muted">Net Salary</small>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class="salary-card">
        <form action="{{ route('admin.payroll.salarySheet') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-2">
                <label>Month</label>
                <select name="month" class="form-control form-control-sm">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ ($month ?? date('n')) == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label>Year</label>
                <select name="year" class="form-control form-control-sm">
                    @php
                        $currentYear = date('Y');
                    @endphp

                    @for ($y = 2000; $y <= 2099; $y++)
                        <option value="{{ $y }}"
                            {{ ($year ?? $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label>Department</label>
                <select name="department_id" class="form-control form-control-sm">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Employee</label>
                <select name="employee_id" class="form-control form-control-sm">
                    <option value="">All Employees</option>
                    @foreach($employees ?? [] as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->employee_id ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-sm btn-primary w-50 mr-1"><i class="bx bx-search"></i> Filter</button>
                 <a href="{{ route('admin.payroll.salarySheet') }}" class="btn btn-sm btn-secondary"><i class="bx bx-reset"></i> Reset</a>
            </div>
            </form>

        <div class="row mt-3 mb-3">
            <div class="col-md-12">
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#generateSalaryModal"><i class="bx bx-calculator"></i> Generate Salary</button>
                <a href="{{ route('admin.payroll.salarySheetExport', ['month' => $month, 'year' => $year]) }}" class="btn btn-sm btn-info"><i class="bx bx-printer"></i> Print All</a>
            </div>
        </div>

        <!-- Generate Salary Modal -->
        <div class="modal fade" id="generateSalaryModal" tabindex="-1" aria-labelledby="generateSalaryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="generateSalaryModalLabel">Process Monthly Salary</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.payroll.process') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Select Month</label>
                                <select name="month" class="form-control" required>
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Select Year</label>
                                <select name="year" class="form-control" required>
                                    @php
                                        $currentYear = date('Y');
                                    @endphp

                                    @for ($y = 2000; $y <= 2099; $y++)
                                        <option value="{{ $y }}"
                                            {{ ($year ?? $currentYear) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="reprocess" id="reprocess" value="1">
                                    <label class="form-check-label" for="reprocess">
                                        Reprocess (Update existing salary)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success"><i class="bx bx-calculator"></i> Process Salary</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th rowspan="2">SL</th>
                        <th rowspan="2">Emp ID</th>
                        <th rowspan="2">Name</th>
                        <th rowspan="2">Dept</th>
                        <th rowspan="2">Desig</th>
                        <th rowspan="2">Basic</th>
                        <th colspan="4">Allowances</th>
                        <th colspan="4">Deductions</th>
                        <th rowspan="2">Gross</th>
                        <th rowspan="2">Net Salary</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>House</th>
                        <th>Medical</th>
                        <th>Transport</th>
                        <th>Other</th>
                        <th>Absent</th>
                        <th>Late</th>
                        <th>Tax/PF</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totals = [
                        'basic' => 0,
                        'house' => 0,
                        'medical' => 0,
                        'transport' => 0,
                        'other' => 0,
                        'absent' => 0,
                        'late' => 0,
                        'tax_pf' => 0,
                        'total_deduction' => 0,
                        'gross' => 0,
                        'net' => 0
                    ];
                    @endphp
                    @forelse($salaries as $i => $salary)
                    @php
                        $user = $salary->user;
                        $empInfo = $user ?? null;
                        $totals['basic'] += $salary->basic_salary ?? 0;
                        $totals['house'] += $salary->house_rent ?? 0;
                        $totals['medical'] += $salary->medical_allowance ?? 0;
                        $totals['transport'] += $salary->transport_allowance ?? 0;
                        $totals['other'] += $salary->other_allowance ?? 0;
                        $totals['absent'] += $salary->absent_deduction ?? 0;
                        $totals['late'] += $salary->late_deduction ?? 0;
                        $totals['tax_pf'] += ($salary->tax ?? 0) + ($salary->provident_fund ?? 0);
                        $totals['total_deduction'] += $salary->total_deduction ?? 0;
                        $totals['gross'] += $salary->gross_salary ?? 0;
                        $totals['net'] += $salary->net_salary ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $empInfo->employee_id ?? '--' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user && $user->photo)
                                    <img src="{{ asset('uploads/user_photo/' . $user->photo) }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover; margin-right: 8px;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 30px; height: 30px; background-color: {{ random_color($user->id ?? 0) }}; margin-right: 8px; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $user->name ?? '--' }}</span>
                            </div>
                        </td>
                        <td>{{ $empInfo->department->name ?? '--' }}</td>
                        <td>{{ $empInfo->designation->name ?? '--' }}</td>
                        <td>৳{{ number_format($salary->basic_salary ?? 0, 0) }}</td>
                        <td>৳{{ number_format($salary->house_rent ?? 0, 0) }}</td>
                        <td>৳{{ number_format($salary->medical_allowance ?? 0, 0) }}</td>
                        <td>৳{{ number_format($salary->transport_allowance ?? 0, 0) }}</td>
                        <td>৳{{ number_format($salary->other_allowance ?? 0, 0) }}</td>
                        <td class="text-danger">৳{{ number_format($salary->absent_deduction ?? 0, 0) }}</td>
                        <td class="text-danger">৳{{ number_format($salary->late_deduction ?? 0, 0) }}</td>
                        <td class="text-danger">৳{{ number_format(($salary->tax ?? 0) + ($salary->provident_fund ?? 0), 0) }}</td>
                        <td class="text-danger">৳{{ number_format($salary->total_deduction ?? 0, 0) }}</td>
                        <td>৳{{ number_format($salary->gross_salary ?? 0, 0) }}</td>
                        <td><strong>৳{{ number_format($salary->net_salary ?? 0, 0) }}</strong></td>
                        <td>
                            @if($salary->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($salary->payment_status == 'held')
                                <span class="badge bg-danger">Held</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.payroll.paySlip', $salary->id) }}" class="btn btn-sm btn-primary" target="blank">
                                <i class="bx bx-receipt"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="18" class="text-center text-muted py-4">No salary data available</td>
                    </tr>
                    @endforelse
                    @if(count($salaries ?? []) > 0)
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <th colspan="5" class="text-end">Total:</th>
                        <th>৳{{ number_format($totals['basic'], 0) }}</th>
                        <th>৳{{ number_format($totals['house'], 0) }}</th>
                        <th>৳{{ number_format($totals['medical'], 0) }}</th>
                        <th>৳{{ number_format($totals['transport'], 0) }}</th>
                        <th>৳{{ number_format($totals['other'], 0) }}</th>
                        <th class="text-danger">৳{{ number_format($totals['absent'], 0) }}</th>
                        <th class="text-danger">৳{{ number_format($totals['late'], 0) }}</th>
                        <th class="text-danger">৳{{ number_format($totals['tax_pf'], 0) }}</th>
                        <th class="text-danger">৳{{ number_format($totals['total_deduction'], 0) }}</th>
                        <th>৳{{ number_format($totals['gross'], 0) }}</th>
                        <th>৳{{ number_format($totals['net'], 0) }}</th>
                        <th colspan="2"></th>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
