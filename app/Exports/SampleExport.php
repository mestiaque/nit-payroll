<?php
// app/Exports/SampleExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class SampleExport implements FromCollection
{
    public function collection()
    {
        return new Collection([
            ['Name', 'Email', 'Department'],
            ['John Doe', 'john@example.com', 'HR'],
            ['Jane Smith', 'jane@example.com', 'Finance'],
        ]);
    }
}
