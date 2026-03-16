<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceApproval;
use App\Models\AttendanceMachineLog;
use App\Models\Attribute;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Roaster;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceManagementController extends Controller
{
    /**
     * Check if given time is within card accept window
     */
    private function withinCardAcceptWindow(?Shift $shift, Carbon $time): bool
    {
        if (!$shift || !$shift->card_accept_from || !$shift->card_accept_to) return true;

        $from = Carbon::parse($time->toDateString().' '.$shift->card_accept_from, 'Asia/Dhaka');
        $to   = Carbon::parse($time->toDateString().' '.$shift->card_accept_to, 'Asia/Dhaka');
        if ($shift->card_accept_to_next_day) $to->addDay();

        return $time->betweenIncluded($from, $to);
    }

    /**
     * Get shift start datetime
     */
    private function shiftStartDateTime(?Shift $shift, Carbon $time): ?Carbon
    {
        if (!$shift || !$shift->shift_starting_time) return null;
        return Carbon::parse($time->toDateString().' '.$shift->shift_starting_time, 'Asia/Dhaka');
    }

    /**
     * Get shift end datetime
     */
    private function shiftEndDateTime(?Shift $shift, Carbon $time): ?Carbon
    {
        if (!$shift || !$shift->shift_closing_time) return null;

        $end = Carbon::parse($time->toDateString().' '.$shift->shift_closing_time, 'Asia/Dhaka');
        if ($shift->shift_closing_time_next_day) $end->addDay();
        return $end;
    }

    /**
     * Get overtime end datetime
     */
    private function overtimeEndDateTime(?Shift $shift, Carbon $time): ?Carbon
    {
        if (!$shift) return null;

        $candidates = [];

        if ($shift->over_time_allowed_up_to) {
            $t = Carbon::parse($time->toDateString().' '.$shift->over_time_allowed_up_to, 'Asia/Dhaka');
            if ($shift->over_time_allowed_up_to_next_day) $t->addDay();
            $candidates[] = $t;
        }

        if ($shift->over_time_1_allowed_up_to) {
            $t = Carbon::parse($time->toDateString().' '.$shift->over_time_1_allowed_up_to, 'Asia/Dhaka');
            if ($shift->over_time_1_allowed_up_to_next_day) $t->addDay();
            $candidates[] = $t;
        }

        if (empty($candidates)) return null;

        return collect($candidates)->sort()->last();
    }

    /**
     * Check if weekly overtime is allowed for the given day
     */
    private function isWeeklyOvertimeAllowed(?Shift $shift, Carbon $time): bool
    {
        if (!$shift || !$shift->weekly_overtime_allowed) return false;

        $map = [
            6 => 'weekly_ot_sat',
            0 => 'weekly_ot_sun',
            1 => 'weekly_ot_mon',
            2 => 'weekly_ot_tue',
            3 => 'weekly_ot_wed',
            4 => 'weekly_ot_thu',
        ];

        $key = $map[$time->dayOfWeek] ?? null;
        if (!$key) return true;

        return (bool) $shift->{$key};
    }

    /**
     * Apply shift logic to attendance
     */
    private function applyShiftLogic(Attendance $attendance, ?Shift $shift): void
    {
        if (!$attendance->in_time || !$shift) {
            $attendance->status = 'Present';
            $attendance->in_minutes = 0;
            $attendance->overtime_minutes = 0;
            return;
        }

        // Calculate status based on shift start time
        $shiftStart = $this->shiftStartDateTime($shift, $attendance->in_time);
        if ($shiftStart) {
            $attendance->status = $attendance->in_time->greaterThan($shiftStart) ? 'Late' : 'Present';
        } else {
            $attendance->status = 'Present';
        }

        // Calculate in_minutes and overtime_minutes if out_time exists
        if ($attendance->in_time && $attendance->out_time) {
            $attendance->in_minutes = $attendance->in_time->diffInMinutes($attendance->out_time);

            $shiftEnd = $this->shiftEndDateTime($shift, $attendance->in_time);
            $otEnd    = $this->overtimeEndDateTime($shift, $attendance->in_time);

            if ($shiftEnd && $this->isWeeklyOvertimeAllowed($shift, $attendance->in_time)) {
                $cap = $otEnd ?? $attendance->out_time;
                $out = $attendance->out_time->lt($cap) ? $attendance->out_time : $cap;

                $attendance->overtime_minutes = $out->greaterThan($shiftEnd)
                    ? $shiftEnd->diffInMinutes($out)
                    : 0;
            } else {
                $attendance->overtime_minutes = 0;
            }
        } else {
            $attendance->in_minutes = 0;
            $attendance->overtime_minutes = 0;
        }
    }

    /**
     * Roaster Management - List
     */
    public function roasterIndex(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');
        $department_id = $request->department_id;

        $query = Roaster::with(['user.department', 'user.designation', 'shift'])
            ->where('roster_date', $date);

        if ($department_id) {
            $query->whereHas('user', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }
        if ($request->employee_id) {
            $query->where('user_id', $request->employee_id);
        }

        $roasters = $query->get();

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $shifts = Shift::where('status', 'active')->get();
        $employees = User::where('employee_status', 'active')->filterBy('employee')->get();

        return view(adminTheme().'attendance.roaster_index', compact('roasters', 'departments', 'shifts', 'employees', 'date'));
    }

    /**
     * Roaster Management - Create
     */
    public function roasterCreate(Request $request)
    {
        $employees = User::where('employee_status', 'active')
            ->with(['department', 'designation'])
            ->filterBy('employee')
            ->get();

        $shifts = Shift::where('status', 'active')->get();

        return view(adminTheme().'attendance.roaster_create', compact('employees', 'shifts'));
    }

    /**
     * Roaster Management - Store
     */
    public function roasterStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'employee_ids' => 'required|array',
                'roster_date' => 'required|date',
                'shift_id' => 'required|exists:shifts,id',
            ]);

            $shift = Shift::findOrFail($request->shift_id);

            foreach ($request->employee_ids as $employeeId) {
                Roaster::updateOrCreate(
                    [
                        'user_id' => $employeeId,
                        'roster_date' => $request->roster_date,
                    ],
                    [
                        'shift_id' => $request->shift_id,
                        'in_time' => $shift->in_time,
                        'out_time' => $shift->out_time,
                        'day_type' => $request->day_type ?? 'working',
                        'remarks' => $request->remarks,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.attendance.roaster.index')->with('success', 'Roaster created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->with('error', 'Error creating roaster: ' . $e->getMessage());
        }
    }

    /**
     * Roaster Management - Bulk Update
     */
    public function roasterBulkUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'user_ids' => 'required|array',
                'shift_id' => 'required|exists:shifts,id',
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $shift = Shift::findOrFail($request->shift_id);
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                foreach ($request->user_ids as $userId) {
                    Roaster::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'roster_date' => $currentDate->format('Y-m-d'),
                        ],
                        [
                            'shift_id' => $request->shift_id,
                            'in_time' => $shift->in_time,
                            'out_time' => $shift->out_time,
                            'day_type' => 'working',
                        ]
                    );
                }
                $currentDate->addDay();
            }

            DB::commit();
            return back()->with('success', 'Roasters updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->with('error', 'Error updating roasters: ' . $e->getMessage());
        }
    }

    /**
     * Roaster Management - Update
     */
    public function roasterUpdate(Request $request, $id)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'roster_date' => 'required|date',
        ]);

        try {
            $roaster = Roaster::findOrFail($id);
            $shift = Shift::findOrFail($request->shift_id);

            $roaster->update([
                'shift_id' => $request->shift_id,
                'in_time' => $shift->in_time,
                'out_time' => $shift->out_time,
                'roster_date' => $request->roster_date,
            ]);

            return back()->with('success', 'Roaster updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating roaster: ' . $e->getMessage());
        }
    }

    /**
     * Roaster Management - Destroy
     */
    public function roasterDestroy($id)
    {
        try {
            $roaster = Roaster::findOrFail($id);
            $roaster->delete();

            return back()->with('success', 'Roaster deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting roaster: ' . $e->getMessage());
        }
    }

    /**
     * Process Attendance from Machine Data
     */
    public function processAttendance(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        // Check if date is a holiday
        $holiday = Holiday::getHoliday($date);

        // Get weekly offday from settings (default is Friday = 5)
        $offdaySetting = Attribute::where('type', 21)->where('status', 'active')->first();
        $offdayNumber = $offdaySetting ? array_search($offdaySetting->name, ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']) : 5;
        $isWeeklyOff = Carbon::parse($date)->dayOfWeek == $offdayNumber;

        // Get machine logs for the date
        $machineLogs = DB::table('attendance_machine_logs')
            ->where('punch_date', $date)
            ->orderBy('punch_time')
            ->get();

        if ($machineLogs->isEmpty() && !$holiday && !$isWeeklyOff) {
            return back()->with('error', 'No machine data found for this date!');
        }

        DB::beginTransaction();
        try {
            $processedData = [];

            // If it's a holiday, mark all users as holiday
            if ($holiday) {
                $allUsers = User::where('status', 1)->get();
                foreach ($allUsers as $user) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'date' => $date,
                        ],
                        [
                            'status' => 'holiday',
                            'remarks' => 'Holiday: ' . $holiday->title,
                        ]
                    );
                }
                $processedData[] = 'Holiday: ' . $holiday->title;
            }

            // If it's weekly off day (configured in settings), mark all users as weekly_off
            if ($isWeeklyOff && !$holiday) {
                $allUsers = User::where('status', 1)->get();
                foreach ($allUsers as $user) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'date' => $date,
                        ],
                        [
                            'status' => 'weekly_off',
                            'remarks' => 'Weekly Off (' . ($offdaySetting ? $offdaySetting->name : 'Friday') . ')',
                        ]
                    );
                }
                $processedData[] = 'Weekly Off (' . ($offdaySetting ? $offdaySetting->name : 'Friday') . ')';
            }

            // Group by user for regular attendance processing
            $groupedLogs = $machineLogs->groupBy('user_id');

            foreach ($groupedLogs as $userId => $logs) {
                $firstPunch = $logs->first();
                $lastPunch = $logs->last();

                $user = User::with('shift')->find($userId);
                if (!$user) continue;

                $inTime  = Carbon::parse($date . ' ' . $firstPunch->punch_time);
                $outTime = $logs->count() > 1 ? Carbon::parse($date . ' ' . $lastPunch->punch_time) : null;

                // Attendance model er computeFromShift() diye shift apply kore sab calculate kori
                $tempAtt           = new Attendance();
                $tempAtt->in_time  = $inTime;
                $tempAtt->out_time = $outTime;

                if ($user->shift) {
                    $tempAtt->computeFromShift($user->shift);
                } else {
                    // Shift configured na thakle basic fallback
                    $wm = $outTime ? (int) $inTime->diffInMinutes($outTime) : 0;
                    $tempAtt->attributes['in_minutes']       = $wm;
                    $tempAtt->attributes['work_hour']        = round($wm / 60, 2);
                    $tempAtt->attributes['late_time']        = 0;
                    $tempAtt->attributes['early_out']        = 0;
                    $tempAtt->attributes['overtime_minutes'] = 0;
                    $tempAtt->attributes['overtime']         = 0;
                    $tempAtt->attributes['status']           = 'present';
                }

                // Computed values diye attendance save kori
                Attendance::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'date'    => $date,
                    ],
                    [
                        'in_time'          => $inTime->format('H:i:s'),
                        'out_time'         => $outTime ? $outTime->format('H:i:s') : null,
                        'in_minutes'       => $tempAtt->attributes['in_minutes'],
                        'work_hour'        => $tempAtt->attributes['work_hour'],
                        'late_time'        => $tempAtt->attributes['late_time'],
                        'early_out'        => $tempAtt->attributes['early_out'],
                        'overtime_minutes' => $tempAtt->attributes['overtime_minutes'],
                        'overtime'         => $tempAtt->attributes['overtime'],
                        'status'           => strtolower($tempAtt->attributes['status']),
                    ]
                );

                $processedData[] = [
                    'user'         => $user,
                    'in_time'      => $inTime,
                    'out_time'     => $outTime,
                    'late_minutes' => $tempAtt->attributes['late_time'],
                    'work_hours'   => $tempAtt->attributes['work_hour'],
                ];
            }

            DB::commit();
            return back()->with('success', 'Attendance processed successfully for ' . count($processedData) . ' employees!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing attendance: ' . $e->getMessage());
        }
    }

    /**
     * Daily Attendance Report
     */
    public function dailyAttendanceReport(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');
        $status = $request->status; // all, present, late, leave, absent
        $department_id = $request->department_id;

        $query = Attendance::with(['user.department', 'user.designation'])
            ->where('date', $date);

        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        if ($department_id) {
            $query->whereHas('user.department', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $attendances = $query->get();

        // Get statistics
        $stats = [
            'total' => Attendance::where('date', $date)->count(),
            'present' => Attendance::where('date', $date)->where('status', 'present')->count(),
            'late' => Attendance::where('date', $date)->where('status', 'late')->count(),
            'absent' => Attendance::where('date', $date)->where('status', 'absent')->count(),
            'leave' => Attendance::where('date', $date)->where('status', 'leave')->count(),
            'weekly_off' => Attendance::where('date', $date)->where('status', 'weekly_off')->count(),
            'holiday' => Attendance::where('date', $date)->where('status', 'holiday')->count(),
        ];

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();

        return view(adminTheme().'attendance.daily_report', compact('attendances', 'stats', 'date', 'departments'));
    }

    /**
     * Machine Log Index - View attendance machine logs
     */
    public function machineLogIndex(Request $request)
    {
        // Get start and end dates
        $date = $request->date ?? Carbon::today()->format('Y-m-d');
        $to_date = $request->to_date ?? $date; // if to_date not given, use date

        $user_id = $request->user_id;
        $device_sn = $request->device_sn;

        $query = AttendanceMachineLog::with('user');

        // Filter by date range
        if ($date && $to_date) {
            $query->whereBetween('log_time', [
                Carbon::parse($date)->startOfDay(),
                Carbon::parse($to_date)->endOfDay()
            ]);
        }

        // Filter by user
        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        // Filter by device
        if ($device_sn) {
            $query->where('device_sn', $device_sn);
        }

        $logs = $query->orderBy('log_time', 'desc')
                ->paginate(50)
                ->appends([
                    'date' => $date,
                    'to_date' => $to_date,
                    'user_id' => $user_id,
                    'device_sn' => $device_sn,
                ]);

        // Get unique device serial numbers
        $devices = AttendanceMachineLog::distinct()->pluck('device_sn')->filter();

        // Get employees for filter
        $employees = User::where('employee_status', 'active')
            ->with(['department', 'designation'])
            ->filterBy('employee')
            ->get();

        return view(adminTheme().'attendance.machine_log_index', compact(
            'logs', 'date', 'to_date', 'devices', 'employees', 'user_id', 'device_sn'
        ));
    }

    /**
     * Attendance Summary
     */
    public function attendanceSummary(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $user_id = $request->user_id;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        if ($user_id) {
            // Individual employee summary
            $user = User::findOrFail($user_id);

            $attendances = Attendance::where('user_id', $user_id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $summary = [
                'total_days' => $startDate->daysInMonth,
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'leave' => $attendances->where('status', 'leave')->count(),
                'weekly_off' => $attendances->where('status', 'weekly_off')->count(),
                'holiday' => $attendances->where('status', 'holiday')->count(),
                'total_work_hours' => round($attendances->sum('in_minutes') / 60, 2),
                'total_overtime' => $attendances->sum('overtime'),
            ];

            return view(adminTheme().'attendance.individual_summary', compact('user', 'attendances', 'summary', 'month', 'year'));
        } else {
            // All employees summary
            $employees = User::where('status', 'active')->filterBy('employee')->get();

            $summaries = [];
            foreach ($employees as $employee) {
                $attendances = Attendance::where('user_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $summaries[] = [
                    'employee' => $employee,
                    'present' => $attendances->whereIn('status', ['present', 'late'])->count(),
                    'late' => $attendances->where('status', 'late')->count(),
                    'absent' => $attendances->where('status', 'absent')->count(),
                    'leave' => $attendances->where('status', 'leave')->count(),
                    'work_hours' => round($attendances->sum('in_minutes') / 60, 2),
                    'overtime' => $attendances->sum('overtime'),
                ];
            }

            return view(adminTheme().'attendance.all_summary', compact('summaries', 'month', 'year'));
        }
    }

    /**
     * Monthly Attendance Report
     */
    public function monthlyAttendanceReport(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $department_id = $request->department_id;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $query = User::where('status', 'active')->filterBy('employee');

        if ($department_id) {
            $query->whereHas('department', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $employees = $query->get();

        $reportData = [];
        foreach ($employees as $employee) {
            $attendances = Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->keyBy('date');

            $dailyData = [];
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->format('Y-m-d');
                $attendance = $attendances->get($dateStr);

                $dailyData[] = [
                    'date' => $currentDate->copy(),
                    'status' => $attendance ? $attendance->status : 'absent',
                    'in_time' => $attendance ? $attendance->in_time : null,
                    'out_time' => $attendance ? $attendance->out_time : null,
                ];

                $currentDate->addDay();
            }

            $reportData[] = [
                'employee' => $employee,
                'daily_data' => $dailyData,
                'present_count' => $attendances->whereIn('status', ['present', 'late'])->count(),
                'absent_count' => $attendances->where('status', 'absent')->count(),
                'leave_count' => $attendances->where('status', 'leave')->count(),
            ];
        }

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();

        return view(adminTheme().'attendance.monthly_report', compact('reportData', 'month', 'year', 'departments'));
    }

    /**
     * Monthly Attendance Summary (Grid View with Date Range)
     */
    public function monthlyAttendanceSummary(Request $request)
    {
        // Default to current month
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $employee_id = $request->employee_id;
        $department_id = $request->department_id;

        // Get employees - same as daily attendance
        $query = User::filterBy('employee')->whereIn('status', [0, 1]);

        if ($employee_id) {
            $query->where('id', $employee_id);
        }

        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        $employees = $query->orderBy('employee_id')->get();

        // Get holidays for the date range (using from_date and to_date)
        $holidays = Holiday::where('status', 'active')
            ->whereDate('from_date', '<=', $endDate->format('Y-m-d'))
            ->whereDate('to_date', '>=', $startDate->format('Y-m-d'))
            ->get();

        // Build holiday dates array
        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $current = Carbon::parse($holiday->from_date);
            $toDate = Carbon::parse($holiday->to_date);
            while ($current->lte($toDate)) {
                $holidayDates[$current->format('Y-m-d')] = $holiday->title;
                $current->addDay();
            }
        }

        // Get leaves for the date range
        $leaves = Leave::where('status', 'approved')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orWhere(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                        ->where('end_date', '>=', $endDate->format('Y-m-d'));
                });
            })
            ->get();

        // Get weekly offday
        $offdaySetting = Attribute::where('type', 21)->where('status', 'active')->first();
        $offdayNumber = $offdaySetting ? array_search($offdaySetting->name, ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']) : 5;

        // Build date range array
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateRange[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // Get attendance data - using in_time like daily attendance
        $attendances = Attendance::whereIn('user_id', $employees->pluck('id'))
            ->whereDate('in_time', '>=', $startDate->format('Y-m-d'))
            ->whereDate('in_time', '<=', $endDate->format('Y-m-d'))
            ->get()
            ->groupBy(function ($item) {
                return $item->user_id . '_' . Carbon::parse($item->in_time)->format('Y-m-d');
            });

        // Build report data
        $reportData = [];
        foreach ($employees as $employee) {
            $dailyData = [];
            $presentCount = 0;
            $absentCount = 0;
            $leaveCount = 0;
            $holidayCount = 0;
            $weeklyOffCount = 0;
            $lateCount = 0;
            $incompleteCount = 0;

            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                // Use the global getAttendanceStatusByDate function for consistency
                $statusData = $employee->getAttendanceStatusByDate($dateStr);
                $status = $statusData['status'];
                $statusClass = $statusData['class'];
                $label = $statusData['label'];

                // Count based on status
                switch ($status) {
                    case 'incomplete':
                        $incompleteCount++;
                        break;
                    case 'present':
                        $presentCount++;
                        break;
                    case 'late':
                        $lateCount++;
                        break;
                    case 'leave':
                        $leaveCount++;
                        break;
                    case 'absent':
                        $absentCount++;
                        break;
                    case 'holiday':
                        $holidayCount++;
                        break;
                    case 'offday':
                    case 'weekly_off':
                        $weeklyOffCount++;
                        break;
                }

                $dailyData[] = [
                    'date' => $date,
                    'day' => $date->format('j'),
                    'dayName' => $date->format('d M'),
                    'status' => $label,
                    'status_class' => $statusClass,
                ];
            }

            $reportData[] = [
                'employee' => $employee,
                'daily_data' => $dailyData,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'leave_count' => $leaveCount,
                'incomplete_count' => $incompleteCount,
                'late_count' => $lateCount,
                'holiday_count' => $holidayCount,
                'weekly_off_count' => $weeklyOffCount,
                'total_days' => count($dateRange),
            ];
        }

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $allEmployees = User::filterBy('employee')->whereIn('status', [0, 1])->orderBy('employee_id')->get();

        return view(adminTheme().'attendance.monthly_summary', compact(
            'reportData',
            'dateRange',
            'startDate',
            'endDate',
            'departments',
            'allEmployees',
            'employee_id',
            'department_id'
        ));
    }

    /**
     * Print Monthly Attendance Summary
     */
    public function monthlyAttendanceSummaryPrint(Request $request)
    {
        // Default to current month
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $employee_id = $request->employee_id;
        $department_id = $request->department_id;

        // Get employees - same as monthly summary
        $query = User::filterBy('employee')->whereIn('status', [0, 1]);

        if ($employee_id) {
            $query->where('id', $employee_id);
        }

        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        $employees = $query->orderBy('employee_id')->get();

        // Get holidays for the date range
        $holidays = Holiday::where('status', 'active')
            ->whereDate('from_date', '<=', $endDate->format('Y-m-d'))
            ->whereDate('to_date', '>=', $startDate->format('Y-m-d'))
            ->get();

        // Build holiday dates array
        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $current = Carbon::parse($holiday->from_date);
            $toDate = Carbon::parse($holiday->to_date);
            while ($current->lte($toDate)) {
                $holidayDates[$current->format('Y-m-d')] = $holiday->title;
                $current->addDay();
            }
        }

        // Get leaves for the date range
        $leaves = Leave::where('status', 'approved')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orWhere(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                        ->where('end_date', '>=', $endDate->format('Y-m-d'));
                });
            })
            ->get();

        // Get weekly offday
        $offdaySetting = Attribute::where('type', 21)->where('status', 'active')->first();
        $offdayNumber = $offdaySetting ? array_search($offdaySetting->name, ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']) : 5;

        // Build date range array
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateRange[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // Get attendance data
        $attendances = Attendance::whereIn('user_id', $employees->pluck('id'))
            ->whereDate('in_time', '>=', $startDate->format('Y-m-d'))
            ->whereDate('in_time', '<=', $endDate->format('Y-m-d'))
            ->get()
            ->groupBy(function ($item) {
                return $item->user_id . '_' . Carbon::parse($item->in_time)->format('Y-m-d');
            });

        // Build report data
        $reportData = [];
        foreach ($employees as $employee) {
            $dailyData = [];
            $presentCount = 0;
            $absentCount = 0;
            $leaveCount = 0;
            $holidayCount = 0;
            $weeklyOffCount = 0;
            $lateCount = 0;
            $incompleteCount = 0;

            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                // Use the global getAttendanceStatusByDate function for consistency
                $statusData = $employee->getAttendanceStatusByDate($dateStr);
                $status = $statusData['status'];
                $statusClass = $statusData['class'];
                $label = $statusData['label'];

                // Count based on status
                switch ($status) {
                    case 'incomplete':
                            $incompleteCount++;
                            break;
                    case 'present':
                        $presentCount++;
                        break;
                    case 'late':
                        $lateCount++;
                        break;
                    case 'leave':
                        $leaveCount++;
                        break;
                    case 'absent':
                        $absentCount++;
                        break;
                    case 'holiday':
                        $holidayCount++;
                        break;
                    case 'offday':
                    case 'weekly_off':
                        $weeklyOffCount++;
                        break;
                }

                $dailyData[] = [
                    'date' => $date,
                    'day' => $date->format('j'),
                    'dayName' => $date->format('d M'),
                    'status' => $label,
                    'status_class' => $statusClass,
                ];
            }

            $reportData[] = [
                'employee' => $employee,
                'daily_data' => $dailyData,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'leave_count' => $leaveCount,
                'late_count' => $lateCount,
                'holiday_count' => $holidayCount,
                'weekly_off_count' => $weeklyOffCount,
                'incomplete_count' => $incompleteCount,
                'total_days' => count($dateRange),
            ];
        }

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $allEmployees = User::filterBy('employee')->whereIn('status', [0, 1])->orderBy('employee_id')->get();

        return view(adminTheme().'attendance.monthly_summary_print', compact(
            'reportData',
            'dateRange',
            'startDate',
            'endDate',
            'departments',
            'allEmployees',
            'employee_id',
            'department_id'
        ));
    }

    /**
     * Export Monthly Attendance Summary to Excel
     */
    public function monthlyAttendanceSummaryExport(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $employee_id = $request->employee_id;
        $department_id = $request->department_id;

        // Get employees
        $query = User::filterBy('employee')->whereIn('status', [0, 1]);

        if ($employee_id) {
            $query->where('id', $employee_id);
        }

        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        $employees = $query->orderBy('employee_id')->get();

        // Build date range array
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateRange[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // Build report data
        $reportData = [];
        foreach ($employees as $employee) {
            $dailyData = [];
            $presentCount = 0;
            $absentCount = 0;
            $leaveCount = 0;
            $holidayCount = 0;
            $lateCount = 0;
            $incompleteCount = 0;

            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');

                // Use getAttendanceStatusByDate function
                $statusData = $employee->getAttendanceStatusByDate($dateStr);
                $status = $statusData['status'];
                $label = $statusData['label'];

                // Count based on status
                switch ($status) {
                    case 'incomplete':
                        $incompleteCount++;
                        break;
                    case 'present':
                        $presentCount++;
                        break;
                    case 'late':
                        $lateCount++;
                        break;
                    case 'leave':
                        $leaveCount++;
                        break;
                    case 'absent':
                        $absentCount++;
                        break;
                    case 'holiday':
                        $holidayCount++;
                        break;
                }

                $dailyData[] = [
                    'date' => $date,
                    'status' => $label,
                ];
            }

            $reportData[] = [
                'employee' => $employee,
                'daily_data' => $dailyData,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'leave_count' => $leaveCount,
                'late_count' => $lateCount,
                'holiday_count' => $holidayCount,
                'incomplete_count' => $incompleteCount,
            ];
        }

        // Export to Excel
        $export = new \App\Exports\MonthlyAttendanceExport($reportData, $dateRange, $startDate, $endDate);

        $filename = 'monthly_attendance_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    public function attendanceExport(Request $request)
    {
        $startDate = Carbon::parse($request->start_date ?? Carbon::now()->startOfMonth());
        $endDate = Carbon::parse($request->end_date ?? Carbon::now()->endOfMonth());
        $department_id = $request->department_id;
        $employee_id = $request->employee_id;

        // Get employees
        $employees = User::filterBy('employee')
            ->whereIn('status', [0, 1])
            ->with(['designation', 'department']);

        if ($department_id) {
            $employees = $employees->where('department_id', $department_id);
        }
        if ($employee_id) {
            $employees = $employees->where('id', $employee_id);
        }
        $employees = $employees->get();

        // Get attendances
        $attendances = Attendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['present', 'absent', 'late'])
            ->get();

        // Return Excel export
        $export = new \App\Exports\AttendanceExport(
            $attendances,
            $startDate->format('m'),
            $startDate->format('Y')
        );

        return \Maatwebsite\Excel\Facades\Excel::download($export, 'attendance_'.$startDate->format('Ym').'.xlsx');
    }

    /**
     * Individual Employee Attendance Report (Grid View)
     */
    public function individualAttendanceReport(Request $request)
    {
        $employee_id = $request->employee_id;
        $month = $request->month ?? Carbon::now()->format('Y-m');

        // Parse month
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        // Use same employee source as monthly summary for consistent results
        $employees = User::filterBy('employee')
            ->whereIn('status', [0, 1])
            ->orderBy('employee_id')
            ->get();

        $employee = null;
        $dailyData = [];
        $summary = null;
        $gridData = null;
        $totalWorkingHours = null;
        $totalLateMinutes = null;
        $totalOvertime = null;

        if ($employee_id) {
            $employee = User::with(['department', 'designation', 'shift'])->findOrFail($employee_id);

            // Fetch attendance records by both date and in_time for robust time details.
            $attendanceRecords = Attendance::where('user_id', $employee_id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->whereNotNull('in_time')
                              ->whereDate('in_time', '>=', $startDate->format('Y-m-d'))
                              ->whereDate('in_time', '<=', $endDate->format('Y-m-d'));
                        });
                })
                ->orderBy('date')
                ->orderBy('in_time')
                ->get()
                ->groupBy(function ($item) {
                    if (!empty($item->date)) {
                        return Carbon::parse($item->date)->format('Y-m-d');
                    }

                    return $item->in_time ? Carbon::parse($item->in_time)->format('Y-m-d') : null;
                });

            // Build date range
            $dateRange = [];
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateRange[] = $currentDate->copy();
                $currentDate->addDay();
            }

            $gridData = [
                'dateRange' => $dateRange,
                'daily_data' => [],
                'present_count' => 0,
                'absent_count' => 0,
                'late_count' => 0,
                'leave_count' => 0,
                'holiday_count' => 0,
                'incomplete_count' => 0,
            ];

            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');

                // Use the same function as monthly summary for consistency
                $statusData = $employee->getAttendanceStatusByDate($dateStr);
                $status = $statusData['status'];
                $statusClass = $statusData['class'];
                $label = $statusData['label'];

                $attendance = $attendanceRecords->get($dateStr)?->first();

                $inTime = '-';
                $outTime = '-';
                if ($attendance && $attendance->in_time) {
                    $inTime = Carbon::parse($attendance->in_time)->format('H:i');
                }
                if ($attendance && $attendance->out_time) {
                    $outTime = Carbon::parse($attendance->out_time)->format('H:i');
                }

                $workingHours = '-';
                $lateMinutes = 0;
                $overtimeHours = null;

                if ($attendance) {
                    // Shift apply kore model theke calculate kori (same logic everywhere)
                    $displayVals   = $attendance->getDisplayValues($employee->shift ?? null);
                    $hasInAndOut   = !empty($attendance->in_time) && !empty($attendance->out_time);
                    $workingHours  = $hasInAndOut ? round((float) $displayVals['working_hours'], 2) : '-';
                    $lateMinutes   = $displayVals['late_minutes'];
                    $overtimeHours = $displayVals['overtime'] > 0 ? $displayVals['overtime'] : null;
                }

                $detailStatus = $label;
                if ($status === 'late') {
                    $detailStatus = 'LT';
                } elseif ($status === 'offday') {
                    $detailStatus = 'WO';
                }

                $dailyData[] = [
                    'date' => $date,
                    'day' => $date->format('j'),
                    'day_name' => $date->format('D'),
                    'status' => $detailStatus,
                    'status_class' => $statusClass,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'working_hours' => $workingHours,
                    'late_minutes' => $lateMinutes,
                    'overtime' => $overtimeHours,
                ];

                $gridData['daily_data'][] = [
                    'date' => $date,
                    'status' => $label,
                    'status_class' => $statusClass,
                ];

                // Count based on status (same as monthly summary)
                switch ($status) {
                    case 'present':
                        $gridData['present_count']++;
                        break;
                    case 'late':
                        $gridData['late_count']++;
                        break;
                    case 'absent':
                        $gridData['absent_count']++;
                        break;
                    case 'leave':
                        $gridData['leave_count']++;
                        break;
                    case 'holiday':
                    case 'offday':
                    case 'weekly_off':
                        // Combine holiday and weekly_off as H for summary display
                        $gridData['holiday_count']++;
                        break;
                    case 'incomplete':
                        $gridData['incomplete_count']++;
                        break;
                }
            }

            $summary = [
                'present' => $gridData['present_count'],
                'late' => $gridData['late_count'],
                'absent' => $gridData['absent_count'],
                'leave' => $gridData['leave_count'],
                'holiday' => $gridData['holiday_count'],
                'total' => count($dateRange),
            ];

            // Calculate totals
            $totalWorkingHours = 0;
            $totalLateMinutes = 0;
            $totalOvertime = 0;

            foreach ($attendanceRecords as $dateKey => $dayAttendances) {
                foreach ($dayAttendances as $att) {
                    // Same model method diye compute kori — show ar total same logic
                    $dv = $att->getDisplayValues($employee->shift ?? null);
                    $totalWorkingHours += $dv['working_hours'];
                    $totalLateMinutes  += $dv['late_minutes'];
                    $totalOvertime     += $dv['overtime'];
                }
            }
        }

        return view(adminTheme().'attendance.individual_report', compact(
            'employee',
            'employees',
            'employee_id',
            'month',
            'dailyData',
            'gridData',
            'summary',
            'totalWorkingHours',
            'totalLateMinutes',
            'totalOvertime',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Print Individual Attendance Report
     */
    public function individualAttendanceReportPrint(Request $request)
    {
        $employee_id = $request->employee_id;
        $month = $request->month ?? Carbon::now()->format('Y-m');

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $employees = User::where('customer', 1)
            ->where('employee_status', 'active')
            ->orderBy('name')
            ->get();

        $employee = null;
        $dailyData = [];
        $summary = null;
        $gridData = null;

        if ($employee_id) {
            $employee = User::with(['department', 'designation'])->findOrFail($employee_id);

            $holidays = Holiday::where('status', 'active')
                ->whereDate('from_date', '<=', $endDate->format('Y-m-d'))
                ->whereDate('to_date', '>=', $startDate->format('Y-m-d'))
                ->get();

            $holidayDates = [];
            foreach ($holidays as $holiday) {
                $current = Carbon::parse($holiday->from_date);
                $toDate = Carbon::parse($holiday->to_date);
                while ($current->lte($toDate)) {
                    $holidayDates[$current->format('Y-m-d')] = $holiday->title;
                    $current->addDay();
                }
            }

            $offdaySetting = Attribute::where('type', 21)->where('status', 'active')->first();
            $offdayNumber = $offdaySetting ? array_search($offdaySetting->name, ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']) : 5;

            $leaves = Leave::where('user_id', $employee_id)
                ->where('status', 'approved')
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                    });
                })
                ->get();

            $attendances = Attendance::where('user_id', $employee_id)
                ->whereDate('in_time', '>=', $startDate->format('Y-m-d'))
                ->whereDate('in_time', '<=', $endDate->format('Y-m-d'))
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->in_time)->format('Y-m-d');
                });

            $dateRange = [];
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateRange[] = $currentDate->copy();
                $currentDate->addDay();
            }

            $presentCount = 0;
            $absentCount = 0;
            $leaveCount = 0;
            $holidayCount = 0;
            $lateCount = 0;

            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                $isHoliday = isset($holidayDates[$dateStr]);
                $isWeeklyOff = ($dayOfWeek == $offdayNumber);

                $leave = $leaves->filter(function($l) use ($date) {
                    return $date->between($l->start_date, $l->end_date);
                })->first();

                $attendance = $attendances->get($dateStr)?->first();

                $status = '';
                $statusClass = '';
                $inTime = '-';
                $outTime = '-';

                if ($leave) {
                    $status = 'L';
                    $statusClass = 'leave';
                    $leaveCount++;
                } elseif ($attendance) {
                    if (in_array($attendance->status, ['present', 'late'])) {
                        $status = $attendance->status == 'late' ? 'LT' : 'P';
                        $statusClass = $attendance->status == 'late' ? 'late' : 'present';
                        $presentCount++;
                        if ($attendance->status == 'late') $lateCount++;

                        if ($attendance->in_time) {
                            $inTime = is_string($attendance->in_time) ?
                                substr($attendance->in_time, 0, 5) :
                                Carbon::parse($attendance->in_time)->format('H:i');
                        }
                        if ($attendance->out_time) {
                            $outTime = is_string($attendance->out_time) ?
                                substr($attendance->out_time, 0, 5) :
                                Carbon::parse($attendance->out_time)->format('H:i');
                        }
                    } elseif ($attendance->status == 'absent') {
                        $status = 'A';
                        $statusClass = 'absent';
                        $absentCount++;
                    } elseif (in_array($attendance->status, ['holiday', 'weekly_off'])) {
                        $status = 'H';
                        $statusClass = 'holiday';
                        $holidayCount++;
                    }
                } elseif ($isHoliday) {
                    $status = 'H';
                    $statusClass = 'holiday';
                    $holidayCount++;
                } elseif ($isWeeklyOff) {
                    $status = 'H';
                    $statusClass = 'holiday';
                    $holidayCount++;
                } else {
                    $status = 'A';
                    $statusClass = 'absent';
                    $absentCount++;
                }

                $dailyData[] = [
                    'date' => $date,
                    'day' => $date->format('j'),
                    'day_name' => $date->format('D'),
                    'status' => $status,
                    'status_class' => $statusClass,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'working_hours' => ($attendance && $attendance->working_hour) ? $attendance->working_hour : '-',
                    'late_minutes' => ($attendance && $attendance->late_minutes) ? $attendance->late_minutes : 0,
                    'overtime' => ($attendance && $attendance->overtime_hour) ? $attendance->overtime_hour : null,
                ];
            }

            $summary = [
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => $absentCount,
                'leave' => $leaveCount,
                'holiday' => $holidayCount,
                'total' => count($dateRange),
            ];
            
            // Build grid data
            $gridData = [
                'dateRange' => $dateRange,
                'daily_data' => [],
                'present_count' => 0,
                'absent_count' => 0,
                'late_count' => 0,
                'leave_count' => 0,
                'holiday_count' => 0,
                'incomplete_count' => 0,
            ];
            
            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');
                $statusData = $employee->getAttendanceStatusByDate($dateStr);
                $status = $statusData['status'];
                $statusClass = $statusData['class'];
                $label = $statusData['label'];
                
                $gridData['daily_data'][] = [
                    'date' => $date,
                    'status' => $label,
                    'status_class' => $statusClass,
                ];
                
                switch ($status) {
                    case 'present':
                        $gridData['present_count']++;
                        break;
                    case 'late':
                        $gridData['late_count']++;
                        break;
                    case 'absent':
                        $gridData['absent_count']++;
                        break;
                    case 'leave':
                        $gridData['leave_count']++;
                        break;
                    case 'holiday':
                    case 'offday':
                    case 'weekly_off':
                        $gridData['holiday_count']++;
                        break;
                    case 'incomplete':
                        $gridData['incomplete_count']++;
                        break;
                }
            }
            
            $totalWorkingHours = 0;
            $totalLateMinutes = 0;
            $totalOvertime = 0;
            
            foreach ($attendances as $dateKey => $dayAttendances) {
                foreach ($dayAttendances as $att) {
                    if ($att->working_hour) {
                        $totalWorkingHours += floatval($att->working_hour);
                    }
                    if ($att->late_minutes) {
                        $totalLateMinutes += intval($att->late_minutes);
                    }
                    if ($att->overtime_hour) {
                        $totalOvertime += floatval($att->overtime_hour);
                    }
                }
            }
        }

        return view(adminTheme().'attendance.individual_report_print', compact(
            'employee',
            'employees',
            'employee_id',
            'month',
            'dailyData',
            'gridData',
            'summary',
            'totalWorkingHours',
            'totalLateMinutes',
            'totalOvertime',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Last 7/10 Days Absent Report
     */
    public function absentReport(Request $request)
    {
        $days = $request->days ?? 7; // 7 or 10
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days);

        $employees = User::where('status', 'active')->filterBy('employee')->get();

        $absentEmployees = [];
        foreach ($employees as $employee) {
            $absentCount = Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'absent')
                ->count();

            if ($absentCount >= $days) {
                $absentEmployees[] = [
                    'employee' => $employee,
                    'absent_days' => $absentCount,
                ];
            }
        }

        return view(adminTheme().'attendance.absent_report', compact('absentEmployees', 'days', 'startDate', 'endDate'));
    }

    /**
     * Invalid In Time & No Out Time Report
     */
    public function invalidAttendanceReport(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        // No out time
        $noOutTime = Attendance::with(['user'])
            ->where('date', $date)
            ->whereNull('out_time')
            ->whereNotNull('in_time')
            ->get();

        // Invalid in time (very late or very early)
        $invalidInTime = Attendance::with(['user'])
            ->where('date', $date)
            ->where('late_time', '>', 120) // More than 2 hours late
            ->get();

        return view(adminTheme().'attendance.invalid_report', compact('noOutTime', 'invalidInTime', 'date'));
    }

        /**
     * Manual Attendance List
     */
    public function manualIndex(Request $request)
    {
        $selectedDate = $request->date ?? Carbon::today()->format('Y-m-d');

        $query = Attendance::with('user')->where('via', '2');
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->department_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->date) {
            $query->where('date', $request->date);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(2);

        $employees = User::where('status', 1)->filterBy('employee')->get();
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();

        $attendanceUserIds = Attendance::whereDate('date', $selectedDate)->pluck('user_id');
        $approvedLeaveUserIds = Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', $selectedDate)
            ->whereDate('end_date', '>=', $selectedDate)
            ->pluck('user_id');
        $pendingApprovalUserIds = AttendanceApproval::whereDate('attendance_date', $selectedDate)
            ->where('status', 'pending')
            ->pluck('user_id');

        $blockedUserIds = $attendanceUserIds
            ->merge($approvedLeaveUserIds)
            ->merge($pendingApprovalUserIds)
            ->unique()
            ->values();

        $absentEmployees = User::where('status', 1)
            ->filterBy('employee')
            ->whereNotIn('id', $blockedUserIds)
            ->with('department')
            ->orderBy('name')
            ->get();

        return view('admin.attendance.manual_index', compact('attendances', 'employees', 'departments', 'absentEmployees', 'selectedDate'));
    }

    /**
     * Manual Attendance Create Form
     */
    public function manualCreate()
    {
        $employees = User::where('status', 1)->filterBy('employee')->get();
        return view('admin.attendance.manual_create', compact('employees'));
    }

    /**
     * Manual Attendance Store
     */
    public function manualStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'in_time' => 'required',
            'out_time' => 'required',
            'remarks' => 'nullable|string',
        ]);

        $attendance = Attendance::where('user_id', $request->user_id)
            ->where('date', $request->date)
            ->first();

        if ($attendance) {
            return redirect()->route('admin.attendance.manual.index')->with('error', 'Attendance already exists for this employee on selected date.');
        }

        $pendingApproval = AttendanceApproval::where('user_id', $request->user_id)
            ->whereDate('attendance_date', $request->date)
            ->where('status', 'pending')
            ->exists();

        if ($pendingApproval) {
            return redirect()->route('admin.attendance.manual.index')->with('error', 'A pending approval request already exists for this employee on selected date.');
        }

        AttendanceApproval::create([
            'user_id' => $request->user_id,
            'attendance_date' => $request->date,
            'in_time' => Carbon::parse($request->date . ' ' . $request->in_time, 'Asia/Dhaka'),
            'out_time' => Carbon::parse($request->date . ' ' . $request->out_time, 'Asia/Dhaka'),
            'original_status' => 'Absent',
            'requested_status' => 'Present',
            'reason' => 'Manual attendance request' . ($request->remarks ? ': ' . $request->remarks : ''),
            'status' => 'pending',
        ]);

        return redirect()->route('admin.attendance.manual.index')->with('success', 'Attendance request submitted for approval. It will be counted after approval.');
    }

    /**
     * Manual Attendance Edit Form
     */
    public function manualEdit($id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);
        $employees = \App\Models\User::where('status', 1)->filterBy('employee')->get();
        return view('admin.attendance.manual_edit', compact('attendance', 'employees'));
    }

    /**
     * Manual Attendance Update
     */
    public function manualUpdate(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'in_time' => 'required',
            'out_time' => 'required',
            'remarks' => 'nullable|string',
        ]);

        $attendance = Attendance::findOrFail($id);

        $approval = AttendanceApproval::where('user_id', $attendance->user_id)
            ->whereDate('attendance_date', $request->date)
            ->where('status', 'pending')
            ->first();

        $payload = [
            'user_id' => $attendance->user_id,
            'attendance_date' => $request->date,
            'in_time' => Carbon::parse($request->date . ' ' . $request->in_time, 'Asia/Dhaka'),
            'out_time' => Carbon::parse($request->date . ' ' . $request->out_time, 'Asia/Dhaka'),
            'original_status' => $attendance->status,
            'requested_status' => $attendance->status ?: 'Present',
            'reason' => 'Manual attendance edit request' . ($request->remarks ? ': ' . $request->remarks : ''),
            'status' => 'pending',
        ];

        if ($approval) {
            $approval->update($payload);
        } else {
            AttendanceApproval::create($payload);
        }

        return redirect()->route('admin.attendance.manual.index')->with('success', 'Attendance edit request submitted for approval. Attendance will update after approval.');
    }

    /**
     * Manual Attendance Delete
     */
    public function manualDestroy($id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);
        $attendance->delete();
        return redirect()->route('admin.attendance.manual.index')->with('success', 'Attendance deleted successfully.');
    }
}
