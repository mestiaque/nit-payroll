<?php
namespace App\Exports;

use App\Exports\BaseExport;

class DailyAttendanceExport extends BaseExport
{
    /**
     * @param array|\Illuminate\Support\Collection $data
     */
    public function __construct($data)
    {
        // data coming from controller already uses human-readable keys
        // so use them directly here; BaseExport will look them up on the row arrays
        $columns = [
            'Employee ID'   => 'Employee ID',
            'Name'          => 'Name',
            'Designation'   => 'Designation',
            'Department'    => 'Department',
            'Employee Type' => 'Employee Type',
            'In Time'       => 'In Time',
            'Out Time'      => 'Out Time',
            'Work Hour'     => 'Work Hour',
            'Status'        => 'Status',
            'Date'          => 'Date',
            'Day'           => 'Day',
        ];

        // pass the data directly; BaseExport will handle SL and property access
        // also allow passing objects by converting to arrays with same keys
        $formatted = collect($data)->map(function($r){
            return is_object($r) ? (array) $r : $r;
        })->toArray();

        parent::__construct($formatted, $columns, 'Daily Attendance Report');
    }
}
