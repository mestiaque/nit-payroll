<?php
namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Employee::with('department')->get()->map(function($employee) {
            return [
                $employee->name,
                $employee->email,
                $employee->department->name ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Department',
        ];
    }
}
