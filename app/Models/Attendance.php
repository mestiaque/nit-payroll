<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //Models Information Data
    /********
     *
     * Column:
     *
     * id               =bigint(20):None,
     * name             =varchar(250):null,
     * created_at       =timestamp:null
     * updated_at       =timestamp:null
     *
     *
     ****/

     protected $casts = [
        'in_time' => 'datetime',
        'out_time' => 'datetime',
        'in_minutes' => 'integer',
        'overtime_minutes' => 'integer',
    ];

    protected $fillable = [
        'id',
        'user_id',
        'in_time',
        'out_time',
        'in_minutes',
        'overtime_minutes',
        'latitude',
        'longitude',
        'status',
        'via',
        'verify_type',
        'device_sn',
        'created_at',
        'updated_at',
        'date',
        'work_hour',
        'late_time',
        'early_out',
        'overtime',
        'location_lat',
        'location_long',
        'remarks'
    ];


    public function user(){
    	return $this->belongsTo(User::class,'user_id');
    }

    public function machinery(){
        return $this->hasMany(CompanyMachinery::class,'company_id');
    }

    // Backward-compatible alias: many old reports read work_hour.
    public function getWorkHourAttribute($value)
    {
        if (array_key_exists('in_minutes', $this->attributes) && $this->attributes['in_minutes'] !== null) {
            return round(((float) $this->attributes['in_minutes']) / 60, 2);
        }

        return $value;
    }

    // If legacy code sets work_hour, persist into canonical in_minutes.
    public function setWorkHourAttribute($value)
    {
        $this->attributes['work_hour'] = $value;

        if (!array_key_exists('in_minutes', $this->attributes) || $this->attributes['in_minutes'] === null) {
            $this->attributes['in_minutes'] = $value !== null && $value !== ''
                ? (int) round(((float) $value) * 60)
                : 0;
        }
    }

    // Legacy alias for view/controller code that expects late_minutes.
    public function getLateMinutesAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        return $this->attributes['late_time'] ?? 0;
    }

    // Legacy alias for view/controller code that expects overtime_hour.
    public function getOvertimeHourAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        return $this->attributes['overtime'] ?? 0;
    }

    /**
     * Display-ready computed values — shift apply kore recalculate kore return kore.
     * Jodi shift paoa jai ebong in_time ache, computeFromShift() diye fresh calculate hoy.
     * Nomanual fallback: stored DB values use hoy.
     *
     * Returns: ['working_hours' => float, 'late_minutes' => int, 'overtime' => float]
     */
    public function getDisplayValues(?Shift $shift = null): array
    {
        if ($shift && $this->in_time) {
            // Clone kore compute kori jate original model dirty na hoy
            $clone = clone $this;
            $clone->computeFromShift($shift);
            return [
                'working_hours' => (float) ($clone->attributes['work_hour']  ?? 0),
                'late_minutes'  => (int)   ($clone->attributes['late_time']  ?? 0),
                'overtime'      => (float) ($clone->attributes['overtime']   ?? 0),
            ];
        }

        // Fallback: DB-stored values use kori
        $workingMinutes = isset($this->attributes['in_minutes']) ? (int) $this->attributes['in_minutes'] : null;
        $workingHours   = $workingMinutes !== null
            ? round($workingMinutes / 60, 2)
            : (float) ($this->attributes['work_hour'] ?? 0);

        return [
            'working_hours' => $workingHours,
            'late_minutes'  => (int)   ($this->attributes['late_time'] ?? 0),
            'overtime'      => (float) ($this->attributes['overtime']  ?? 0),
        ];
    }

    /**
     * Shift apply kore late_time, in_minutes, overtime_minutes, work_hour,
     * overtime, early_out, status — sab manually calculate kore set kore.
     *
     * Calculation rules:
     *  - late_time     = in_time minus red_marking_on (grace cutoff) in minutes, 0 if on time
     *  - in_minutes    = out_time minus in_time in total minutes (actual working time)
     *  - overtime_mins = out_time minus shift_closing_time in minutes, capped by over_time_allowed_up_to
     *  - early_out     = shift_closing_time minus out_time in minutes if left early, else 0
     *  - work_hour     = in_minutes / 60  (hours, 2 decimal)
     *  - overtime      = overtime_minutes / 60 (hours, 2 decimal)
     *  - status        = 'Late' | 'Present'
     *
     * Does NOT call save() — caller must save() after calling this.
     */
    public function computeFromShift(Shift $shift): void
    {
        /** @var Carbon|null $inTime */
        $inTime  = $this->in_time  ? Carbon::parse($this->in_time)  : null;
        /** @var Carbon|null $outTime */
        $outTime = $this->out_time ? Carbon::parse($this->out_time) : null;

        // No in_time — reset everything
        if (!$inTime) {
            $this->attributes['status']           = 'Absent';
            $this->attributes['late_time']        = 0;
            $this->attributes['in_minutes']       = 0;
            $this->attributes['work_hour']        = 0;
            $this->attributes['overtime_minutes'] = 0;
            $this->attributes['overtime']         = 0;
            $this->attributes['early_out']        = 0;
            return;
        }

        $date = $inTime->toDateString();

        // ----------------------------------------------------------------
        // 1. Late Time Calculation
        //    red_marking_on = grace period cutoff (e.g. 08:10 when shift starts 08:00)
        //    If in_time > red_marking_on → Late, late_time = in_time − red_marking_on
        //    Fallback: if red_marking_on not set, use shift_starting_time directly
        // ----------------------------------------------------------------
        $lateMinutes = 0;
        $status      = 'Present';

        $graceCutoff = null;
        if ($shift->red_marking_on) {
            $graceCutoff = Carbon::parse($date . ' ' . $shift->red_marking_on, 'Asia/Dhaka');
        } elseif ($shift->shift_starting_time) {
            $graceCutoff = Carbon::parse($date . ' ' . $shift->shift_starting_time, 'Asia/Dhaka');
        }

        if ($graceCutoff && $inTime->greaterThan($graceCutoff)) {
            $lateMinutes = (int) $graceCutoff->diffInMinutes($inTime);
            $status      = 'Late';
        }

        $this->attributes['late_time'] = $lateMinutes;
        $this->attributes['status']    = $status;

        // ----------------------------------------------------------------
        // 2. Working Minutes (in_minutes)
        //    Actual time spent: out_time − in_time in minutes
        //    If no out_time, treat as 0 (punch-out pending)
        // ----------------------------------------------------------------
        $workingMinutes = 0;
        if ($outTime) {
            $workingMinutes = (int) $inTime->diffInMinutes($outTime);
        }

        $this->attributes['in_minutes'] = $workingMinutes;
        $this->attributes['work_hour']  = round($workingMinutes / 60, 2);

        // ----------------------------------------------------------------
        // 3. Overtime Minutes
        //    out_time − shift_closing_time if out_time > shift_closing_time
        //    Capped by over_time_allowed_up_to if configured
        // ----------------------------------------------------------------
        $overtimeMinutes = 0;

        if ($outTime && $shift->shift_closing_time) {
            $shiftEnd = Carbon::parse($date . ' ' . $shift->shift_closing_time, 'Asia/Dhaka');
            if ($shift->shift_closing_time_next_day) {
                $shiftEnd->addDay();
            }

            if ($outTime->greaterThan($shiftEnd)) {
                $rawOt = (int) $shiftEnd->diffInMinutes($outTime);

                // Cap at over_time_allowed_up_to (pick the latest OT cap configured)
                $otCapMinutes = null;
                foreach (['over_time_allowed_up_to', 'over_time_1_allowed_up_to'] as $otField) {
                    if ($shift->{$otField}) {
                        $nextDayField = $otField . '_next_day';
                        $otEnd = Carbon::parse($date . ' ' . $shift->{$otField}, 'Asia/Dhaka');
                        if ($shift->{$nextDayField}) {
                            $otEnd->addDay();
                        }
                        $cap = (int) $shiftEnd->diffInMinutes($otEnd);
                        $otCapMinutes = ($otCapMinutes === null) ? $cap : max($otCapMinutes, $cap);
                    }
                }

                $overtimeMinutes = ($otCapMinutes !== null) ? min($rawOt, $otCapMinutes) : $rawOt;
            }
        }

        $this->attributes['overtime_minutes'] = $overtimeMinutes;
        $this->attributes['overtime']         = round($overtimeMinutes / 60, 2);

        // ----------------------------------------------------------------
        // 4. Early Out (minutes left before shift end)
        //    Only applies when employee left before shift_closing_time
        // ----------------------------------------------------------------
        $earlyOut = 0;
        if ($outTime && $shift->shift_closing_time) {
            $shiftEnd = Carbon::parse($date . ' ' . $shift->shift_closing_time, 'Asia/Dhaka');
            if ($shift->shift_closing_time_next_day) {
                $shiftEnd->addDay();
            }
            if ($outTime->lessThan($shiftEnd)) {
                $earlyOut = (int) $outTime->diffInMinutes($shiftEnd);
            }
        }

        $this->attributes['early_out'] = $earlyOut;
    }

    protected static function booted()
    {
        $user = User::filterBy('admin')->get()->pluck('id');
        static::addGlobalScope('hideUser7', function (Builder $builder) use ($user) {
            $builder->whereNotIn('user_id', $user);
        });
    }

}
