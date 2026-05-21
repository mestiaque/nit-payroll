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
    public function handshake(Request $request)
    {
        return response("OK\n", 200)->header('Content-Type', 'text/plain');
    }

    public function getCommand(Request $request)
    {
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    public function deviceReply(Request $request)
    {
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    public function receiveData(Request $request)
    {
        if (!$this->deviceAuthorized($request)) {
            return response('Unauthorized', 401);
        }

        try {
            $rawBody = trim((string) $request->getContent());
            if ($rawBody !== '' && (str_contains($rawBody, "\t") || stripos($rawBody, 'ATTLOG') !== false)) {
                $count = $this->parseAdmsPayload($rawBody, $request->input('SN') ?? $request->query('SN'));

                return response("OK: {$count}\n", 200)->header('Content-Type', 'text/plain');
            }

            Log::info('ZKTeco Data Received', ['payload' => $request->all()]);

            $userId = $request->input('user_id') ?? $request->input('pin');
            $timestamp = $request->input('time') ?? $request->input('timestamp');
            $sn = $request->input('device_sn') ?? $request->input('SN');
            $verifyType = $request->input('type_name');
            $typeCode = $request->input('type_code');

            if (!$userId || !$timestamp) {
                return response('Invalid Data', 400);
            }

            $this->saveMachineLog($userId, $timestamp, $sn, $verifyType, $typeCode);
            $this->saveAttendance($userId, $timestamp, $sn, $verifyType);

            return response("OK\n", 200)->header('Content-Type', 'text/plain');

        } catch (\Exception $e) {
            Log::error('ZKTeco Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response('ERROR', 500);
        }
    }

    private function deviceAuthorized(Request $request): bool
    {
        $expectedToken = config('services.zkteco.token', env('ZKTECO_DEVICE_TOKEN'));
        if (!$expectedToken) {
            return true;
        }

        $provided = $request->header('X-Device-Token')
            ?? $request->input('device_token')
            ?? $request->query('token');

        return hash_equals((string) $expectedToken, (string) $provided);
    }

    private function parseAdmsPayload(string $body, ?string $deviceSn = null): int
    {
        $count = 0;

        foreach (preg_split('/\r\n|\r|\n/', $body) as $line) {
            $line = trim($line);
            if ($line === '' || stripos($line, 'ATTLOG') === 0) {
                continue;
            }

            $parts = preg_split('/\s+/', $line);
            $parts = array_values(array_filter($parts, fn ($p) => $p !== ''));
            if (count($parts) < 2) {
                continue;
            }

            $pin = $parts[0];
            $timestamp = count($parts) >= 3 && preg_match('/\d{2}:\d{2}/', $parts[2])
                ? ($parts[1] . ' ' . $parts[2])
                : $parts[1];

            $verifyType = $parts[3] ?? 'ADMS';
            $typeCode = $parts[4] ?? null;

            $this->saveMachineLog($pin, $timestamp, $deviceSn, $verifyType, $typeCode);
            $this->saveAttendance($pin, $timestamp, $deviceSn, $verifyType);
            $count++;
        }

        return $count;
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

            $attendance = Attendance::where('user_id', $user->id)
                ->where(function ($query) use ($time) {
                    $query->whereDate('date', $time->toDateString())
                        ->orWhere(function ($subQuery) use ($time) {
                            $subQuery->whereNull('date')
                                ->whereDate('created_at', $time->toDateString());
                        });
                })
                ->first();

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->user_id = $user->id;
                $attendance->device_sn = $sn;
                $attendance->via = 1;
                $attendance->verify_type = $verifyType;
            }

            $attendance->date = $attendance->date ?: $time->toDateString();

            $currentIn = $this->attendanceDateTime($attendance->date, $attendance->in_time);
            $currentOut = $this->attendanceDateTime($attendance->date, $attendance->out_time);

            if (!$currentIn || $time->lt($currentIn)) {
                $attendance->in_time = $time->format('H:i:s');
                $currentIn = $this->attendanceDateTime($attendance->date, $attendance->in_time);
            }

            if ((!$currentOut || $time->gt($currentOut)) && $currentIn && $time->gt($currentIn)) {
                $attendance->out_time = $time->format('H:i:s');
                $currentOut = $this->attendanceDateTime($attendance->date, $attendance->out_time);
            }

            if ($currentIn) {
                $shiftStart = $this->shiftStartDateTime($shift, $currentIn);
                if ($shiftStart) {
                    $attendance->status = $currentIn->greaterThan($shiftStart) ? 'Late' : 'Present';
                } else {
                    $attendance->status = 'Present';
                }
            }

            if ($currentIn && $currentOut) {
                $attendance->in_minutes = $currentIn->diffInMinutes($currentOut);

                $shiftEnd = $this->shiftEndDateTime($shift, $currentIn);
                $otEnd    = $this->overtimeEndDateTime($shift, $currentIn);

                if ($shiftEnd && $this->isWeeklyOvertimeAllowed($shift, $currentIn)) {
                    $cap = $otEnd ?? $currentOut;
                    $out = $currentOut->lt($cap) ? $currentOut : $cap;

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

    private function attendanceDateTime(?string $date, $timeValue): ?Carbon
    {
        if (!$timeValue) {
            return null;
        }

        $baseDate = $date ?: Carbon::today('Asia/Dhaka')->toDateString();

        if ($timeValue instanceof Carbon) {
            $timePart = $timeValue->format('H:i:s');
        } else {
            $timePart = Carbon::parse($timeValue, 'Asia/Dhaka')->format('H:i:s');
        }

        return Carbon::parse($baseDate . ' ' . $timePart, 'Asia/Dhaka');
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

    public function softwareIntegration()
    {
        return view(adminTheme().'attendance.softwareIntegration');
    }
}
