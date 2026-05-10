<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeIncrement;
use App\Models\Attribute;
use App\Models\Leave;
use App\Models\SalarySheet;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class EmployeeReportController extends Controller
{
    /**
     * Employee Report - Main List
     */
    public function index(Request $request)
    {
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $designations = Attribute::where('type', 4)->where('status', 'active')->get();

        $query = User::with(['department', 'designation'])
            ->filterBy('employee');

        // Search filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_id', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->department_id) {
            $query->whereHas('department', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->designation_id) {
            $query->whereHas('designation', function($q) use ($request) {
                $q->where('designation_id', $request->designation_id);
            });
        }

        if ($request->employee_status) {
            $status = $request->employee_status;

            if ($status == 'terminated') {
                $query->whereHas('terminations', function($q) {
                    $q->where('status', 'approved');
                });
            } elseif ($status == 'retired') {
                $query->whereHas('retirements', function($q) {
                    $q->where('status', 'approved');
                });
            } elseif ($status == 'probation') {
                $query->whereHas('probations');
            } elseif ($status == 'active') {
                $query->where('status', 1)
                       ->whereDoesntHave('terminations', function($q) {
                           $q->where('status', 'approved');
                       })
                       ->whereDoesntHave('retirements', function($q) {
                           $q->where('status', 'approved');
                       })
                       ->whereDoesntHave('probations');
            } elseif ($status == 'inactive') {
                $query->where('status', 0);
            }
        }

        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        $employees = $query->orderBy('name')->get();

        $status = [
            'total'      => $employees->count(),
            'probation'  => $employees->where('employee_status', 'probation')->count(),
            'active'     => $employees->where('employee_status', 'active')->count(),
            'inactive'   => $employees->where('employee_status', 'inactive')->count(),
            'retired'    => $employees->where('employee_status', 'retired')->count(),
            'terminated' => $employees->where('employee_status', 'terminated')->count(),
        ];

        return view(adminTheme().'reports.employees.index', compact('employees', 'departments', 'designations', 'status'));
    }

    /**
     * Male & Female Employee List
     */
    public function genderWiseReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get(); // Department list

        $query = User::where('customer', '=', 1)->with('department');

        if ($request->gender) {
            $query->where('gender', '=', $request->gender);
        }

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        $employees = $query->get();

        $stats = [
            'male' => User::where('customer', '=', 1)->where('gender', '=', 'male')->count(),
            'female' => User::where('customer', '=', 1)->where('gender', '=', 'female')->count(),
            'other' => User::where('customer', '=', 1)->where('gender', '=', 'other')->count(),
        ];

        return view(adminTheme().'reports.employees.gender_wise', compact('employees', 'stats', 'departments'));
    }

    /**
     * Active/Inactive Employee List
     */
    public function statusWiseReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();

        $query = User::where('customer', '=', 1)->with('department');

        if ($request->status) {
            $query->where('employee_status', '=', $request->status);
        }

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        $employees = $query->get();

        $stats = [
            'active' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')->count(),
            'inactive' => User::where('customer', '=', 1)->where('employee_status', '=', 'inactive')->count(),
            'retired' => User::where('customer', '=', 1)->where('employee_status', '=', 'retired')->count(),
        ];

        return view(adminTheme().'reports.employees.status_wise', compact('employees', 'stats', 'departments'));
    }

    /**
     * Newly Joined Employees
     */
    public function newlyJoinedReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();
        $from_date = $request->from_date ?? Carbon::now()->subMonth()->format('Y-m-d');
        $to_date = $request->to_date ?? Carbon::now()->format('Y-m-d');

        $query = User::where('customer', '=', 1)
            ->with('department')
            ->whereBetween('joining_date', [$from_date, $to_date]);

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        $employees = $query->orderBy('joining_date', 'desc')->get();

        return view(adminTheme().'reports.employees.newly_joined', compact('employees', 'departments'));
    }

    /**
     * Retired Employees List
     */
    public function retiredReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();

        $query = User::where('customer', '=', 1)
            ->with('department')
            ->where('employee_status', '=', 'retired')
            ->whereNotNull('retirement_date');

        if ($request->from_date) {
            $query->where('retirement_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->where('retirement_date', '<=', $request->to_date);
        }

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        $employees = $query->orderBy('retirement_date', 'desc')->get();

        return view(adminTheme().'reports.employees.retired', compact('employees', 'departments'));
    }

    /**
     * Month Wise Increment Report
     */
    public function monthWiseIncrementReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();
        $month = $request->month;

        $query = EmployeeIncrement::with(['user.department']);

        if ($month) {
            $query->whereMonth('effective_date', Carbon::parse($month)->month)
                  ->whereYear('effective_date', Carbon::parse($month)->year);
        }

        if ($request->department_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', '=', $request->department_id);
            });
        }

        if ($request->type) {
            $query->where('type', '=', $request->type);
        }

        $increments = $query->orderBy('effective_date', 'desc')->get();

        return view(adminTheme().'reports.employees.increment', compact('increments', 'departments'));
    }

    /**
     * Service Milestone Completion Report
     */
    public function serviceCompletedReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();
        $years = $request->years; // 1, 3, 5, 10

        $query = User::where('customer', '=', 1)
            ->with('department')
            ->where('employee_status', '=', 'active')
            ->whereNotNull('joining_date');

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        // Filter by service years if specified
        if ($years) {
            $targetDate = Carbon::now()->subYears($years);
            $query->whereDate('joining_date', '<=', $targetDate->format('Y-m-d'));
        }

        $employees = $query->orderBy('joining_date', 'asc')->get();

        // Calculate stats
        $stats = [
            '1_year' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')
                ->whereNotNull('joining_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) >= 1')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) < 3')
                ->count(),
            '3_years' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')
                ->whereNotNull('joining_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) >= 3')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) < 5')
                ->count(),
            '5_years' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')
                ->whereNotNull('joining_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) >= 5')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) < 10')
                ->count(),
            '10_years' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')
                ->whereNotNull('joining_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) >= 10')
                ->count(),
        ];

        return view(adminTheme().'reports.employees.service_completed', compact('employees', 'departments', 'stats'));
    }

    /**
     * Full Bengali Employee List (for Government/Official purposes)
     */
    public function bengaliEmployeeList(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();

        $query = User::where('customer', '=', 1)->with('department');

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        if ($request->status) {
            $query->where('employee_status', '=', $request->status);
        }

        $employees = $query->orderBy('name', 'asc')->get();

        return view(adminTheme().'reports.employees.bengali_list', compact('employees', 'departments'));
    }

    /**
     * Leave Reports - Month & Year Wise
     */
    public function leaveReport(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $leave_type_id = $request->leave_type_id;
        $user_id = $request->user_id;
        $status = $request->status;

        $query = Leave::with(['user', 'leaveType', 'approver']);

        if ($month && $year) {
            $query->whereMonth('start_date', $month)->whereYear('start_date', $year);
        }

        if ($leave_type_id) {
            $query->where('leave_type_id', $leave_type_id);
        }

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $leaves = $query->paginate(50);

        $leaveTypes = Attribute::where('type', 20)->where('status', 'active')->get();
        $employees = User::filterBy('employee')->get();

        return view(adminTheme().'reports.leave_report', compact('leaves', 'leaveTypes', 'employees', 'month', 'year'));
    }

    /**
     * Department/Division/Section-wise Reports
     */
    public function departmentWiseReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();

        $query = User::where('customer', '=', 1)->with('department');

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        if ($request->status) {
            $query->where('employee_status', '=', $request->status);
        }

        $employees = $query->get();

        // Department-wise statistics
        $departmentStats = User::where('customer', '=', 1)
            ->select(
                'department_id',
                DB::raw('COUNT(*) as total_employees'),
                DB::raw('SUM(CASE WHEN employee_status = "active" THEN 1 ELSE 0 END) as active_count'),
                DB::raw('SUM(CASE WHEN employee_status = "inactive" THEN 1 ELSE 0 END) as inactive_count'),
                DB::raw('SUM(CASE WHEN gender = "male" THEN 1 ELSE 0 END) as male_count'),
                DB::raw('SUM(CASE WHEN gender = "female" THEN 1 ELSE 0 END) as female_count'),
                DB::raw('AVG(basic_salary) as avg_salary')
            )
            ->groupBy('department_id')
            ->get()
            ->map(function($item) {
                $dept = Attribute::find($item->department_id);
                $item->department_name = $dept ? $dept->name : 'N/A';
                return $item;
            });

        $summary = $departments->map(function($dept) {
            return [
                'department_name' => $dept->name,
                'employee_count' => User::where('customer', '=', 1)->where('department_id', '=', $dept->id)->filterBy('employee')->count()
            ];
        });

        return view(adminTheme().'reports.employees.department_wise', compact('employees', 'departments', 'departmentStats', 'summary'));
    }

    /**
     * Generate ID Card
     */
    public function idCard(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterBy('employee')->get();

        if ($request->has('employee_ids')) {
            $selectedEmployees = User::whereIn('id', $request->employee_ids)->get();
            return view(adminTheme().'documents.id_card', compact('selectedEmployees', 'employees'));
        }

        return view(adminTheme().'documents.id_card', compact('employees'));
    }

    /**
     * Employee Personal Information Sheet
     */
    public function personalInfoSheet(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterBy('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'documents.personal_info', compact('employee', 'employees'));
        }

        return view(adminTheme().'documents.personal_info', compact('employees'));
    }

    /**
     * Appointment Letter
     */
    public function appointmentLetter(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterBy('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'documents.appointment_letter', compact('employee', 'employees'));
        }

        return view(adminTheme().'documents.appointment_letter', compact('employees'));
    }

    /**
     * Joining Letter
     */
    public function joiningLetter(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterBy('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'documents.joining_letter', compact('employee', 'employees'));
        }

        return view(adminTheme().'documents.joining_letter', compact('employees'));
    }

    /**
     * Increment Letter
     */
    public function incrementLetter(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterBy('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'documents.increment_letter', compact('employee', 'employees'));
        }

        return view(adminTheme().'documents.increment_letter', compact('employees'));
    }

    /**
     * Service Confirmation Letter
     */
    public function confirmationLetter(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterBy('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'documents.confirmation_letter', compact('employee', 'employees'));
        }

        return view(adminTheme().'documents.confirmation_letter', compact('employees'));
    }

    /**
     * Generate Pay Slip
     */
    public function paySlip(Request $request)
    {
        $departments = Attribute::where('type', 3)->where('status', '<>', 'temp')->orderBy('name')->get();
        $sections = Attribute::where('type', 14)->where('status', '<>', 'temp')->orderBy('name')->get();
        $designations = Attribute::where('type', 2)->where('status', '<>', 'temp')->orderBy('name')->get();
        $employeeTypes = Attribute::where('type', 16)->where('status', '<>', 'temp')->orderBy('name')->get();
        $shifts = Shift::orderBy('name_of_shift')->get();

        $employeeQuery = User::where('customer', 1)
            ->where('employee_status', 'active')
            ->filterBy('employee');

        if ($request->filled('department_id')) {
            $employeeQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('section_id')) {
            $employeeQuery->where('section_id', $request->section_id);
        }

        if ($request->filled('designation_id')) {
            $employeeQuery->where('designation_id', $request->designation_id);
        }

        if ($request->filled('employee_type')) {
            $employeeQuery->where('employee_type', $request->employee_type);
        }

        if ($request->filled('shift_id')) {
            $employeeQuery->where('shift_id', $request->shift_id);
        }

        $employees = $employeeQuery->with(['department', 'designation', 'section', 'shift'])->orderBy('name')->get();

        return view(adminTheme().'documents.pay_slip', compact(
            'employees',
            'departments',
            'sections',
            'designations',
            'employeeTypes',
            'shifts'
        ));
    }

    /**
     * Print Pay Slip
     */
    public function paySlipPrint(Request $request)
    {
        $month = $request->month ?? Carbon::now()->format('Y-m');

        $employeeQuery = User::with([
            'department',
            'designation',
            'employeeType',
            'section',
            'shift',
            'line',
        ])->where('customer', 1)->where('employee_status', 'active')->filterBy('employee');

        if ($request->filled('department_id')) {
            $employeeQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('section_id')) {
            $employeeQuery->where('section_id', $request->section_id);
        }

        if ($request->filled('designation_id')) {
            $employeeQuery->where('designation_id', $request->designation_id);
        }

        if ($request->filled('employee_type')) {
            $employeeQuery->where('employee_type', $request->employee_type);
        }

        if ($request->filled('shift_id')) {
            $employeeQuery->where('shift_id', $request->shift_id);
        }

        $employees = collect();
        if ($request->filled('employee_ids')) {
            $typedIds = array_filter(array_map('trim', explode(',', $request->employee_ids)));

            foreach ($typedIds as $typedId) {
                $employee = (clone $employeeQuery)
                    ->where(function ($query) use ($typedId) {
                        $query->where('id', $typedId)
                            ->orWhere('employee_id', $typedId);
                    })
                    ->first();

                if ($employee) {
                    $employees->push($employee);
                }
            }
        } else {
            $employees = $employeeQuery->get();
        }

        abort_if($employees->isEmpty(), 404, 'Employee not found');

        $employeesData = $employees->map(function ($employee) use ($month) {
            return $this->preparePaySlipData($employee, $month);
        })->values();

        return view('admin.documents.pay_slip_print_page', [
            'employeesData' => $employeesData,
            'month' => $month,
        ]);
    }

    /**
     * Prepare pay slip data for print view.
     */
    private function preparePaySlipData(User $employee, string $month): array
    {
        $monthName = date('F Y', strtotime($month . '-01'));
        $year = date('Y', strtotime($month . '-01'));
        $monthNum = date('m', strtotime($month . '-01'));

        $salarySheet = SalarySheet::where('user_id', $employee->id)
            ->where('year', $year)
            ->where('month', $monthNum)
            ->first();

        $basicSalary = $salarySheet?->basic_salary ?? $employee->basic_salary ?? 0;
        $houseRent = $salarySheet?->house_rent ?? $employee->house_rent ?? 0;
        $medical = $salarySheet?->medical_allowance ?? $employee->medical_allowance ?? 0;
        $transport = $salarySheet?->transport_allowance ?? $employee->transport_allowance ?? 0;
        $food = $salarySheet?->food_allowance ?? $employee->food_allowance ?? 0;
        $conveyance = $salarySheet?->conveyance_allowance ?? $employee->conveyance_allowance ?? 0;
        $otherAllowance = $salarySheet?->other_allowance ?? $employee->other_allowance ?? 0;

        $grossSalary = $salarySheet?->gross_salary ?? (
            $basicSalary + $houseRent + $medical + $transport + $food + $conveyance + $otherAllowance
        );

        $attendanceBonus = $salarySheet?->attendance_bonus ?? 0;
        $overtime = $salarySheet?->overtime_amount ?? 0;
        $specialOvertime = $salarySheet?->special_overtime_amount ?? 0;
        $grassTime = $salarySheet?->grass_time_amount ?? 0;
        $bonus = $salarySheet?->bonus_amount ?? 0;
        $otherBonus = $salarySheet?->other_bonus ?? 0;

        $totalEarnings = $grossSalary + $overtime + $specialOvertime + $grassTime + $attendanceBonus + $bonus + $otherBonus;

        $absentDeduction = $salarySheet?->absent_deduction ?? 0;
        $lateDeduction = $salarySheet?->late_deduction ?? 0;
        $taxDeduction = $salarySheet?->tax_deduction ?? 0;
        $pfDeduction = $salarySheet?->provident_fund_deduction ?? $employee->provident_fund ?? 0;
        $loanDeduction = $salarySheet?->loan_deduction ?? 0;
        $advance = $salarySheet?->salary_advance_deduction ?? 0;
        $otherDeductions = $salarySheet?->deduction ?? 0;
        $stampCharge = $salarySheet?->stamp_charge ?? 0;
        $totalDeductions = $absentDeduction + $lateDeduction + $taxDeduction + $pfDeduction + $loanDeduction + $advance + $otherDeductions + $stampCharge;

        $defaultMonthDays = Carbon::parse($month . '-01')->daysInMonth;
        $workingDays = $salarySheet?->working_days ?? $defaultMonthDays;
        $present = $salarySheet?->present_days ?? 0;
        $absent = $salarySheet?->absent_days ?? 0;
        $casual = $salarySheet?->casual_days ?? 0;
        $sick = $salarySheet?->sick_days ?? 0;
        $earned = $salarySheet?->earned_days ?? 0;
        $weekly = $salarySheet?->weekly_off_days ?? 0;
        $festival = $salarySheet?->festival_days ?? 0;
        $general = $salarySheet?->general_days ?? 0;
        $maternity = $salarySheet?->maternity_days ?? 0;
        $holidayDays = $salarySheet?->holiday_days ?? 0;
        $lateDays = $salarySheet?->late_count ?? 0;

        // Fallback to attendance logs when monthly salary sheet is not generated.
        if (!$salarySheet) {
            $attendanceSummary = getMonthlyAttendanceSummary($employee->id, (int) $year, (int) $monthNum, false);

            $present = $attendanceSummary['present'] ?? 0;
            $absent = $attendanceSummary['absent'] ?? 0;
            $lateDays = $attendanceSummary['late'] ?? 0;
            $weekly = $attendanceSummary['weekly_off'] ?? 0;
            $holidayDays = $attendanceSummary['holiday'] ?? 0;

            $daysCounted = $attendanceSummary['days_counted'] ?? $defaultMonthDays;
            $workingDays = max($daysCounted - $weekly - $holidayDays, 0);
        }
        $overtimeHours = $salarySheet?->overtime_hours ?? 0;
        $totalDays = $workingDays + $holidayDays;
        $otRate = $salarySheet?->ot_rate ?? 0;
        $otHour = $salarySheet?->overtime_hours ?? 0;
        $otAmount = $salarySheet?->overtime_amount ?? 0;
        $phoneInternet = $salarySheet?->phone_internet ?? 0;
        $extraFacility = $salarySheet?->extra_facility ?? 0;
        $carFuel = $salarySheet?->car_fuel ?? 0;
        $payable = $totalEarnings;
        $totalSalary = $grossSalary;
        $monthLabel = $monthName;
        $salary = [
            'basic' => $basicSalary,
            'house' => $houseRent,
            'medical' => $medical,
            'transport' => $transport,
            'food' => $food,
            'conveyance' => $conveyance,
            'other' => $otherAllowance,
        ];
        $employeeTypeName = $employee->employeeType?->name;
        if (!$employeeTypeName && !is_null($employee->employee_type) && !is_numeric($employee->employee_type)) {
            $employeeTypeName = $employee->employee_type;
        }

        $employeeData = [
            'company_name' => general()->title ?? 'Company Name',
            'company_address' => general()->address_one ?? '',
            'employee_id' => $employee->employee_id ?: $employee->id,
            'department' => $employee->department?->name ?? '-',
            'section' => $employee->section?->name ?? '-',
            'line' => $employee->line?->name ?? '-',
            'employee_name' => $employee->name,
            'employee_type' => $employeeTypeName ?: '-',
            'designation' => $employee->designation?->name ?? '-',
            'shift' => $employee->shift?->name_of_shift ?? $employee->shift?->name ?? '-',
            'joining_date' => $employee->joining_date
                ? Carbon::parse($employee->joining_date)->format('d-M-Y')
                : '-',
        ];

        return compact(
            'monthName',
            'year',
            'monthNum',
            'salarySheet',
            'employee',
            'employeeData',
            'salary',
            'basicSalary',
            'houseRent',
            'medical',
            'transport',
            'food',
            'conveyance',
            'otherAllowance',
            'grossSalary',
            'attendanceBonus',
            'overtime',
            'specialOvertime',
            'grassTime',
            'bonus',
            'otherBonus',
            'totalEarnings',
            'absentDeduction',
            'lateDeduction',
            'taxDeduction',
            'pfDeduction',
            'loanDeduction',
            'advance',
            'otherDeductions',
            'stampCharge',
            'totalDeductions',
            'workingDays',
            'present',
            'absent',
            'casual',
            'sick',
            'earned',
            'weekly',
            'festival',
            'general',
            'maternity',
            'holidayDays',
            'lateDays',
            'overtimeHours',
            'totalDays',
            'otRate',
            'otHour',
            'otAmount',
            'phoneInternet',
            'extraFacility',
            'carFuel',
            'payable',
            'totalSalary',
            'monthLabel',
        );
    }

    /**
     * Age Identification Letter
     */
    public function ageIdentificationLetter($userId)
    {
        $employee = User::filterBy('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.age_identification', compact('employee'));
        return $pdf->stream('age_identification_' . $employee->name . '.pdf');
    }

    /**
     * Employee Job Ledger
     */
    public function jobLedger($userId)
    {
        $employee = User::with([
            'attendances', 'leaves', 'salarySheets', 'increments'
        ])->filterBy('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.job_ledger', compact('employee'));
        return $pdf->stream('job_ledger_' . $employee->name . '.pdf');
    }

    /**
     * Nominee Form
     */
    public function nomineeForm($userId)
    {
        $employee = User::filterBy('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.nominee_form', compact('employee'));
        return $pdf->stream('nominee_form_' . $employee->name . '.pdf');
    }

    /**
     * Resign Letter
     */
    public function resignLetter($userId)
    {
        $employee = User::filterBy('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.resign_letter', compact('employee'));
        return $pdf->stream('resign_letter_' . $employee->name . '.pdf');
    }

    /**
     * Commitment Letter
     */
    public function commitmentLetter($userId)
    {
        $employee = User::filterBy('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.commitment_letter', compact('employee'));
        return $pdf->stream('commitment_letter_' . $employee->name . '.pdf');
    }

    /**
     * Settlement Letter
     */
    public function settlementLetter($userId)
    {
        $employee = User::filterBy('employee')->findOrFail($userId);

        if (!$employee || !in_array($employee->employee_status, ['retired', 'resigned'])) {
            return back()->with('error', 'Settlement letter only for retired/resigned employees!');
        }

        $pdf = PDF::loadView('admin.documents.settlement_letter', compact('employee'));
        return $pdf->stream('settlement_letter_' . $employee->name . '.pdf');
    }

    /**
     * Job Application Form
     */
    public function jobApplicationForm($userId)
    {
        $employee = User::with([
             'employeeEducation', 'employeeExperience'
        ])->filterBy('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.job_application', compact('employee'));
        return $pdf->stream('job_application_' . $employee->name . '.pdf');
    }
}
