<?php
// app/Http/Controllers/ExcelExportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SampleExport;

class ExcelExportController extends Controller
{
    public function export()
    {
        return Excel::download(new SampleExport, 'sample-data.xlsx');
    }
}
