<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Late pay rate', 'type' => 'late_pay_percentage', 'value' => 90, 'unit' => 'percent', 'description' => 'Percent of daily salary paid on late days'],
            ['name' => 'Absent deduction', 'type' => 'absent_deduction_percentage', 'value' => 100, 'unit' => 'percent', 'description' => 'Percent of daily salary deducted per absent day'],
            ['name' => 'Provident fund', 'type' => 'provident_fund_percentage', 'value' => 5, 'unit' => 'percent', 'description' => 'Employee PF on gross if no manual PF entry'],
            ['name' => 'OT rate multiplier', 'type' => 'overtime_rate_general', 'value' => 1.5, 'unit' => 'multiplier', 'description' => 'Attendance OT hourly multiplier'],
            ['name' => 'Working hours/day', 'type' => 'working_hours_per_day', 'value' => 8, 'unit' => 'hours', 'description' => 'Standard shift hours'],
            ['name' => 'Grace time', 'type' => 'grace_time_minutes', 'value' => 10, 'unit' => 'minutes', 'description' => 'Late grace period'],
            ['name' => 'Tax exempt (annual)', 'type' => 'tax_exempt_limit', 'value' => 0, 'unit' => 'bdt', 'description' => 'Optional annual tax-free amount'],
        ];

        foreach ($defaults as $row) {
            Policy::updateOrCreate(
                ['type' => $row['type']],
                array_merge($row, ['status' => 'active'])
            );
        }
    }
}
