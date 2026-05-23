<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attribute;
use App\Models\Attendance;
use App\Models\EmployeeIncrement;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    /**
     * Display Job Card Print Portal
     */
    public function index(Request $request)
    {
        $month = $request->month ?? Carbon::now()->format('Y-m');
        $selectedUser = null;
        $summary = null;
        $departments = Attribute::where('type', 3)->where('status', '<>', 'temp')->orderBy('name')->get();
        $sections = Attribute::where('type', 14)->where('status', '<>', 'temp')->orderBy('name')->get();
        $designations = Attribute::where('type', 2)->where('status', '<>', 'temp')->orderBy('name')->get();
        $employeeTypes = Attribute::where('type', 16)->where('status', '<>', 'temp')->orderBy('name')->get();
        $shifts = Shift::orderBy('name_of_shift')->get();

        return view('admin.jobcard.index', compact('month', 'selectedUser', 'summary', 'departments', 'sections', 'designations', 'employeeTypes', 'shifts'));
    }

    /**
     * Print Job Card - Opens in new blank page
     */
    public function print(Request $request)
    {
        $month = $request->month ?? Carbon::now()->format('Y-m');
        $employees = collect();

        // Get employee IDs from input
        $userIds = [];
        if ($request->filled('user_ids')) {
            $input = $request->input('user_ids');
            $ids = array_filter(array_map('trim', explode(',', $input)));

            // Search for users by ID or employee_id
            foreach ($ids as $id) {
                $user = User::where('id', $id)->orWhere('employee_id', $id)->first();
                if ($user) {
                    $userIds[] = $user->id;
                }
            }
        }

        $employeeQuery = User::with(['department', 'designation', 'divisionData', 'section', 'shift', 'line', 'grade', 'increments'])
            ->where('status', 1)
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

        // Get employees
        if (!empty($userIds)) {
            $employees = $employeeQuery->whereIn('id', $userIds)->get();
        } else {
            $employees = $employeeQuery->get();
        }

        // Generate job card data for each employee
        $employeesData = [];
        foreach ($employees as $employee) {
            $employeesData[] = $this->generateJobCardData($employee, $month);
        }

        return view('admin.jobcard.print', compact('employeesData', 'month'));
    }

    /**
     * Generate job card data for a single employee
     */
    private function generateJobCardData($employee, $month)
    {
        // Get employee increments
        $increments = EmployeeIncrement::where('user_id', $employee->id)->orderBy('increment_date', 'desc')->get();

        $monthStart = Carbon::parse($month)->startOfMonth();
        $monthEnd = Carbon::parse($month)->endOfMonth();
        $salaryInfo = $employee->salaryInfo();
        $otRate = (float) ($salaryInfo['ot_rate'] ?? 0);

        $attendanceMap = Attendance::where('user_id', $employee->id)
            ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        // Get all dates in month
        $dates = Carbon::parse($month)->startOfMonth()->toPeriod(Carbon::parse($month)->endOfMonth());

        // Calculate daily data using centralized function
        $present = 0;
        $late = 0;
        $absent = 0;
        $leaveCount = 0;
        $holiday = 0;
        $weeklyOff = 0;
        $totalWorkHours = 0;
        $dailyData = [];

        foreach ($dates as $date) {
            $dateStr = $date->format('Y-m-d');

            // Use centralized function for attendance status
            $status = getAttendanceStatus($employee->id, $date);

            $statusCode = $status['status'];
            $inTime = $status['in_time'] ? (is_string($status['in_time']) ? substr($status['in_time'], 0, 5) : Carbon::parse($status['in_time'])->format('H:i')) : '-';
            $outTime = $status['out_time'] ? (is_string($status['out_time']) ? substr($status['out_time'], 0, 5) : Carbon::parse($status['out_time'])->format('H:i')) : '-';
            $workHours = $status['work_hours'] ?? '-';

            // Count based on status
            switch ($statusCode) {
                case 'P':
                    $present++;
                    $totalWorkHours += $status['work_hours'];
                    break;
                case 'LT':
                    $late++;
                    $totalWorkHours += $status['work_hours'];
                    break;
                case 'L':
                    $leaveCount++;
                    break;
                case 'H':
                    $holiday++;
                    break;
                case 'WO':
                    $weeklyOff++;
                    break;
                case 'A':
                    $absent++;
                    break;
            }

            $attendance = $attendanceMap->get($dateStr);
            $dayOtHours = 0;
            if ($attendance) {
                $dayOtHours = (float) ($attendance->overtime ?? 0);
                $dayOtHours += ((float) ($attendance->overtime_minutes ?? 0)) / 60;
            }

            $dailyData[] = [
                'date' => $date->format('d'),
                'day' => substr($date->format('l'), 0, 3),
                'in_time' => $inTime,
                'out_time' => $outTime,
                'work_hours' => $workHours,
                'ot_hours' => round($dayOtHours, 2),
                'status' => $statusCode,
            ];
        }

        $totalOtHours = collect($dailyData)->sum('ot_hours');
        $totalOtAmount = $totalOtHours * $otRate;

        $summary = [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'leave' => $leaveCount,
            'holiday' => $holiday,
            'weekly_off' => $weeklyOff,
            'total_work_hours' => $totalWorkHours,
            'ot_rate' => round($otRate, 2),
            'total_ot_hours' => round($totalOtHours, 2),
            'total_ot_amount' => round($totalOtAmount, 2),
        ];

        return [
            'employee' => $employee,
            'summary' => $summary,
            'dailyData' => $dailyData,
            'increments' => $increments,
        ];
    }
}
