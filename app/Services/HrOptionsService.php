<?php

namespace App\Services;

use Carbon\Carbon;

class HrOptionsService
{
    /**
     * Returns a closure that prepares employee data array for document templates.
     *
     * Usage: $fn = HrOptionsService::getOptionsForEmployee();
     *        $data = $fn($employee, $request, $factory, $salaryKey, $profile, $nominee);
     */
    public static function getOptionsForEmployee(): \Closure
    {
        return function ($employee, $request = null, $factory = null, $salaryKey = null, $profile = null, $nominee = null) {
            if (!$employee) {
                return [];
            }

            $general   = general();
            $language  = data_get($request, 'language', 'bn');
            $isBangla  = $language === 'bn';

            $name = $isBangla
                ? ($employee->bn_name ?: $employee->name)
                : $employee->name;

            $joiningDate = $employee->joining_date
                ? Carbon::parse($employee->joining_date)->format('d-M-Y')
                : 'N/A';

            return [
                'company_name'    => $general->title ?? '',
                'company_address' => $general->address_one ?? $general->address ?? '',
                'employee_name'   => $name,
                'employee_id'     => $employee->employee_id ?: $employee->id,
                'designation'     => $employee->designation?->name ?? 'N/A',
                'department'      => $employee->department?->name ?? 'N/A',
                'section'         => $employee->section?->name ?? 'N/A',
                'joining_date'    => $joiningDate,
                'grade'           => $employee->grade?->name ?? '',
            ];
        };
    }
}
