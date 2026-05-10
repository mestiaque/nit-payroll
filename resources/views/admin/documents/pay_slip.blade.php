@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Pay Slip') }}</title>
@endsection

@push('css')
<style>
    .payslip-shell {
        display: grid;
        gap: 24px;
    }
    .payslip-panel {
        border: 1px solid #e6ebf2;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .payslip-panel .card-header {
        border-bottom: 1px solid #edf2f7;
        background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
        padding: 20px 24px;
    }
    .payslip-title {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #16324f;
    }
    .payslip-subtitle {
        margin: 6px 0 0;
        color: #5f6f82;
        font-size: 14px;
    }
    .payslip-panel .card-body {
        padding: 24px;
    }
    .payslip-form-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 18px;
        align-items: end;
    }
    .payslip-field {
        grid-column: span 4;
    }
    .payslip-field-employee {
        grid-column: span 6;
    }
    .payslip-field-month {
        grid-column: span 3;
    }
    .payslip-field-action {
        grid-column: span 3;
    }
    .payslip-label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #243b53;
    }
    .payslip-input,
    .payslip-select {
        height: 48px;
        border-radius: 12px;
        border: 1px solid #d6dee8;
        box-shadow: none;
        padding: 0 14px;
        font-size: 14px;
    }
    .payslip-input:focus,
    .payslip-select:focus {
        border-color: #2f80ed;
        box-shadow: 0 0 0 4px rgba(47, 128, 237, 0.12);
    }
    .payslip-btn {
        width: 100%;
        min-height: 48px;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #1d72f3 0%, #0f5bd8 100%);
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 10px 22px rgba(29, 114, 243, 0.24);
    }
    .payslip-btn:hover,
    .payslip-btn:focus {
        color: #fff;
        transform: translateY(-1px);
    }
    .payslip-info {
        text-align: center;
        padding: 44px 24px;
    }
    .payslip-info-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #edf4ff;
        color: #175cd3;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .payslip-info h5 {
        margin: 18px 0 8px;
        font-size: 30px;
        font-weight: 700;
        color: #102a43;
    }
    .payslip-info p {
        margin: 0 auto;
        max-width: 640px;
        color: #61758a;
        font-size: 16px;
    }
    .payslip-guide {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-top: 28px;
        text-align: left;
    }
    .payslip-guide-item {
        padding: 18px;
        border: 1px solid #e8eef5;
        border-radius: 14px;
        background: #fff;
    }
    .payslip-guide-item strong {
        display: block;
        margin-bottom: 6px;
        color: #1f3a56;
        font-size: 14px;
    }
    .payslip-guide-item span {
        color: #6b7c93;
        font-size: 13px;
        line-height: 1.5;
    }

    @media (max-width: 1199.98px) {
        .payslip-form-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
        .payslip-field,
        .payslip-field-employee,
        .payslip-field-month,
        .payslip-field-action {
            grid-column: span 3;
        }
    }

    @media (max-width: 767.98px) {
        .payslip-panel .card-header,
        .payslip-panel .card-body {
            padding: 18px;
        }
        .payslip-title {
            font-size: 24px;
        }
        .payslip-form-grid,
        .payslip-guide {
            grid-template-columns: 1fr;
        }
        .payslip-field,
        .payslip-field-employee,
        .payslip-field-month,
        .payslip-field-action {
            grid-column: span 1;
        }
        .payslip-info {
            padding: 28px 18px;
        }
        .payslip-info h5 {
            font-size: 24px;
        }
        .payslip-info p {
            font-size: 14px;
        }
    }

    @media print {
        /* Hide all UI elements */
        .no-print, .breadcrumb-area, .doc-card, form, button, .btn,
        .sidebar, .sidenav, .main-menu, .header-navbar, .footer,
        .sidebar-wrapper, .app-sidebar, .app-header, .app-footer,
        .sidemenu-area, .sidemenu-header, .sidemenu-body,
        .navbar, .top-navbar, .footer-area,
        nav, header, .navbar, .footer-area, .main-menu, .side-menu, .side-nav, .page-header, .page-footer, .layout-header, .layout-footer, .layout-sidebar, .sidebar-menu, .sidebar-footer, .sidebar-header, .sidebar-content, .sidebar, .footer, .header, .sidemenu, .sidemenu-area, .sidemenu-header, .sidemenu-body { display: none !important; }

        /* Show only document content */
        body { margin: 0 !important; padding: 5mm !important; background: white !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .flex-grow-1 { display: block !important; }
        .payslip-container {
            max-width: 100%;
            border: 1px solid #333;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .payslip-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Remove shadows for clean print */
        * { box-shadow: none !important; border: none !important; }
    }
</style>
@endpush

@section('contents')

<div class="breadcrumb-area no-print">
    <h1>Generate Pay Slip</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Documents</li>
        <li class="item">Pay Slip</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1 payslip-shell">

    <div class="card no-print payslip-panel">
        <div class="card-header">
            <h5 class="payslip-title">Pay Slip</h5>
            <p class="payslip-subtitle">Filter employees by master data, pick one employee, and open the printable payslip in a new page.</p>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.documents.paySlipPrint') }}" method="GET" target="_blank" class="payslip-form-grid">
                <div class="payslip-field-employee">
                    <label class="payslip-label">Employee ID(s)</label>
                    <input type="text" name="employee_ids" class="form-control payslip-input" placeholder="e.g., 1,5,10 or E001,E005" value="{{ request('employee_ids', '') }}">
                    <small class="text-muted">Type one employee ID or comma-separated IDs.</small>
                </div>
                <div class="payslip-field">
                    <label class="payslip-label">Department</label>
                    <select name="department_id" class="form-control payslip-select">
                        <option value="">All Departments</option>
                        @foreach(($departments ?? collect()) as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="payslip-field">
                    <label class="payslip-label">Section</label>
                    <select name="section_id" class="form-control payslip-select">
                        <option value="">All Sections</option>
                        @foreach(($sections ?? collect()) as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="payslip-field">
                    <label class="payslip-label">Designation</label>
                    <select name="designation_id" class="form-control payslip-select">
                        <option value="">All Designations</option>
                        @foreach(($designations ?? collect()) as $designation)
                            <option value="{{ $designation->id }}" {{ request('designation_id') == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="payslip-field">
                    <label class="payslip-label">Employee Type</label>
                    <select name="employee_type" class="form-control payslip-select">
                        <option value="">All Employee Types</option>
                        @foreach(($employeeTypes ?? collect()) as $employeeType)
                            <option value="{{ $employeeType->id }}" {{ request('employee_type') == $employeeType->id ? 'selected' : '' }}>{{ $employeeType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="payslip-field">
                    <label class="payslip-label">Shift</label>
                    <select name="shift_id" class="form-control payslip-select">
                        <option value="">All Shifts</option>
                        @foreach(($shifts ?? collect()) as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name_of_shift }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="payslip-field-month">
                    <label class="payslip-label">Month</label>
                    <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" class="form-control payslip-input" required>
                </div>
                <div class="payslip-field-action">
                    <label class="payslip-label">&nbsp;</label>
                    <button type="submit" class="payslip-btn"><i class="bx bx-printer"></i> Print Pay Slip</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card payslip-panel">
        <div class="card-body payslip-info">
            <span class="payslip-info-badge"><i class="bx bx-file"></i> Print Portal</span>
            <h5>Generate Monthly Pay Slips</h5>
            <p>Select an employee, apply any filters you need, and open the final payslip in a separate tab using the shared print master layout.</p>

            <div class="payslip-guide">
                <div class="payslip-guide-item">
                    <strong>Separate Print Page</strong>
                    <span>The print button opens a dedicated payslip page based on the blank print master layout.</span>
                </div>
                <div class="payslip-guide-item">
                    <strong>Master Data Filters</strong>
                    <span>Department, Section, Designation, Employee Type, and Shift come from the HR master data tables.</span>
                </div>
                <div class="payslip-guide-item">
                    <strong>Same Payslip Format</strong>
                    <span>The print output keeps the current payslip layout and dual-copy style from the existing payslip template.</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
