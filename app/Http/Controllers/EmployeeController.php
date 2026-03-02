<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department')->get();
        return view('employee.employee-list', compact('employees'));
    }

    public function export()
    {
        return Excel::download(new EmployeeExport, 'employees.xlsx');
    }
}
