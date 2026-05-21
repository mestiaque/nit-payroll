<?php

namespace App\Services\Payroll;

use App\Models\Policy;
use App\Models\ProvidentFund;
use App\Models\Tax;
use App\Models\User;

class PayrollDeductionService
{
    public static function normalizeMonth(int|string $month): string
    {
        return str_pad((string) (int) $month, 2, '0', STR_PAD_LEFT);
    }

    public static function resolveMonthlyTax(User $employee, int $month, int $year, float $monthlyGross): float
    {
        $monthPadded = self::normalizeMonth($month);

        $taxRecord = Tax::where('user_id', $employee->id)
            ->where('year', $year)
            ->where(function ($q) use ($month, $monthPadded) {
                $q->where('month', $month)
                    ->orWhere('month', $monthPadded)
                    ->orWhere('month', (string) $month);
            })
            ->first();

        if ($taxRecord) {
            return round((float) $taxRecord->net_tax, 2);
        }

        $taxable = $monthlyGross;
        $exemptLimit = (float) Policy::getValue('tax_exempt_limit', 0);
        if ($exemptLimit > 0) {
            $taxable = max(0, $taxable - ($exemptLimit / 12));
        }

        return TaxCalculator::monthlyTaxFromGross($taxable, 0);
    }

    /**
     * Employee PF deduction only (employer share is not deducted from net pay).
     */
    public static function resolveEmployeeProvidentFund(
        User $employee,
        int $month,
        int $year,
        float $monthlyGross
    ): float {
        $monthPadded = self::normalizeMonth($month);

        $pfRecord = ProvidentFund::where('user_id', $employee->id)
            ->where('year', $year)
            ->where(function ($q) use ($month, $monthPadded) {
                $q->where('month', $month)
                    ->orWhere('month', $monthPadded)
                    ->orWhere('month', (string) $month);
            })
            ->where('status', 'active')
            ->first();

        if ($pfRecord) {
            return round((float) $pfRecord->employee_contribution, 2);
        }

        if ($employee->provident_fund > 0) {
            return round((float) $employee->provident_fund, 2);
        }

        $pfPercent = Policy::getProvidentFundPercentage();

        return round($monthlyGross * ($pfPercent / 100), 2);
    }

    public static function latePayMultiplier(): float
    {
        $latePayPct = Policy::getLatePayPercentage();

        return max(0, min(100, $latePayPct)) / 100;
    }

    public static function lateDeductionMultiplier(): float
    {
        return 1 - self::latePayMultiplier();
    }
}
