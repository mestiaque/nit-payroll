<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;

class ZKTecoPushController extends Controller
{
    public function receiveData(Request $request)
    {
        try {
            Log::info("ZKTeco Data Received", ['payload' => $request->all()]);

            $userId     = $request->input('user_id');
            $timestamp  = $request->input('time') ?? $request->input('timestamp');
            $sn         = $request->input('device_sn');
            $verifyType = $request->input('type_name');
            $typeCode   = $request->input('type_code');

            if (!$userId || !$timestamp) {
                return response()->json(['status' => 'error', 'message' => 'Invalid Data'], 400);
            }

            // Save machine log
            $this->saveMachineLog($userId, $timestamp, $sn, $verifyType, $typeCode);

            // Save attendance applying shift rules
            $this->saveAttendance($userId, $timestamp, $sn, $verifyType);

            return response()->json(['status' => 'success', 'message' => 'Attendance Processed'], 200);

        } catch (\Exception $e) {
            Log::error("Shift Action Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function withinCardAcceptWindow(?Shift $shift, Carbon $time): bool
    {
        if (!$shift || !$shift->card_accept_from || !$shift->card_accept_to) return true;

        $from = Carbon::parse($time->toDateString().' '.$shift->card_accept_from, 'Asia/Dhaka');
        $to   = Carbon::parse($time->toDateString().' '.$shift->card_accept_to, 'Asia/Dhaka');
        if ($shift->card_accept_to_next_day) $to->addDay();

        return $time->betweenIncluded($from, $to);
    }

    private function shiftStartDateTime(?Shift $shift, Carbon $time): ?Carbon
    {
        if (!$shift || !$shift->shift_starting_time) return null;
        return Carbon::parse($time->toDateString().' '.$shift->shift_starting_time, 'Asia/Dhaka');
    }

    private function shiftEndDateTime(?Shift $shift, Carbon $time): ?Carbon
    {
        if (!$shift || !$shift->shift_closing_time) return null;

        $end = Carbon::parse($time->toDateString().' '.$shift->shift_closing_time, 'Asia/Dhaka');
        if ($shift->shift_closing_time_next_day) $end->addDay();
        return $end;
    }

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

    private function saveAttendance($userId, $timestamp, $sn, $verifyType)
    {
        try {
            $user = User::with('shift')->where('employee_id', $userId)->first();
            if (!$user) {
                Log::warning("Attendance skipped: UserID $userId not found. SN=$sn");
                return;
            }

            $time = Carbon::parse($timestamp, 'Asia/Dhaka');
            $shift = $user->shift;

            // NOTE: card_accept window is intentionally not enforced here.
            // if ($shift && !$this->withinCardAcceptWindow($shift, $time)) {
            //     Log::info("Attendance skipped: outside card_accept window. User=$userId, Time=$timestamp");
            //     return;
            // }

            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('in_time', $time->toDateString())
                ->first();

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->user_id = $user->id;
                $attendance->device_sn = $sn;
                $attendance->via = 1;
                $attendance->verify_type = $verifyType;
            }

            if (!$attendance->in_time || $time->lt($attendance->in_time)) {
                $attendance->in_time = $time;
            }
            if ($attendance->in_time && (!$attendance->out_time || $time->gt($attendance->out_time))) {
                $attendance->out_time = $time;
            }

            if ($attendance->in_time) {
                $shiftStart = $this->shiftStartDateTime($shift, $attendance->in_time);
                if ($shiftStart) {
                    $attendance->status = $attendance->in_time->greaterThan($shiftStart) ? 'Late' : 'Present';
                } else {
                    $attendance->status = 'Present';
                }
            }

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
            }

            $attendance->save();

            Log::info("Attendance synced: User=$userId, Time=$timestamp, Status={$attendance->status}");

        } catch (\Exception $e) {
            Log::error("Failed to save attendance for UserID $userId: " . $e->getMessage());
        }
    }

    private function saveMachineLog($userId, $timestamp, $sn, $verifyType, $typeCode = null)
    {
        try {
            \App\Models\AttendanceMachineLog::create([
                'device_sn' => $sn,
                'user_id'   => $userId,
                'log_time'  => $timestamp,
                'type_name' => $verifyType,
                'type_code' => $typeCode,
                'created_at' => now(),
            ]);

            Log::info("AttendanceMachineLog created: User=$userId, Time=$timestamp");

        } catch (\Exception $e) {
            Log::error("Failed to create AttendanceMachineLog: " . $e->getMessage());
        }
    }

    public function importAction(Request $request)
    {
        $request->validate([
            'attendance_file' => 'required|file',
        ]);

        $file = $request->file('attendance_file');
        $fileData = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $count = 0;
        foreach ($fileData as $line) {
            $parts = preg_split('/\s+|,/', trim($line));
            $parts = array_values(array_filter($parts, fn($p) => $p !== ''));

            if (count($parts) >= 2) {
                $userId = $parts[0];
                $timestamp = (count($parts) >= 3)
                    ? ($parts[1] . ' ' . $parts[2])
                    : $parts[1];

                $sn         = $parts[3] ?? null;
                $typeCode   = $parts[4] ?? null;
                $verifyType = 'Manual_Import';

                $this->saveMachineLog($userId, $timestamp, $sn, $verifyType, $typeCode);
                $this->saveAttendance($userId, $timestamp, $sn, $verifyType);

                $count++;
            }
        }

        return redirect()->route('admin.importZkteco')
            ->with('success', "$count টি ডাটা সফলভাবে প্রসেস করা হয়েছে।");
    }

    public function import()
    {
        return view(adminTheme().'attendance.logImport');
    }
}
