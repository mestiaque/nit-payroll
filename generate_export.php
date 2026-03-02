<?php
require __DIR__ . '/vendor/autoload.php';

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

$users = [];
for ($i = 1; $i <= 10; $i++) {
    $u = new stdClass;
    $u->employee_id = 100 + $i;
    $u->name = 'User' . $i;
    $u->email = 'user' . $i . '@example.com';
    $u->mobile = '0123456789';
    $u->designation = (object)['name' => 'Desig' . $i];
    $u->department = (object)['name' => 'Dept' . $i];
    $u->section = (object)['name' => 'Sec' . $i];
    $u->line = (object)['name' => 'Line' . $i];
    $u->joining_date = '2020-01-01';
    $u->gross_salary = 5000;
    $u->status = 1;
    $users[] = $u;
}

$export = new UsersExport(collect($users));
$file = '/tmp/test.xlsx';
Excel::store($export, basename($file), dirname($file));
echo "written $file\n";
