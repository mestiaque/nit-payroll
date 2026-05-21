<?php

namespace App\Services\Payroll;

/**
 * Bangladesh individual income tax (FY-style progressive slabs on annual income).
 * Monthly withholding = annual tax / 12.
 */
class TaxCalculator
{
    /** NBR-style simplified slabs (annual BDT). */
    public const SLABS = [
        ['upto' => 350000, 'rate' => 0.00, 'base' => 0],
        ['upto' => 500000, 'rate' => 0.05, 'base' => 0],
        ['upto' => 750000, 'rate' => 0.10, 'base' => 7500],
        ['upto' => 1150000, 'rate' => 0.15, 'base' => 32500],
        ['upto' => 1700000, 'rate' => 0.20, 'base' => 92500],
        ['upto' => PHP_INT_MAX, 'rate' => 0.25, 'base' => 202500],
    ];

    public static function annualTax(float $annualTaxableIncome, float $rebate = 0): float
    {
        $annualTaxableIncome = max(0, $annualTaxableIncome);
        $tax = 0;
        $previousLimit = 0;

        foreach (self::SLABS as $slab) {
            $limit = $slab['upto'];
            if ($annualTaxableIncome <= $previousLimit) {
                break;
            }
            $taxableInSlab = min($annualTaxableIncome, $limit) - $previousLimit;
            if ($taxableInSlab > 0) {
                $tax += $taxableInSlab * $slab['rate'];
            }
            $previousLimit = $limit;
            if ($annualTaxableIncome <= $limit) {
                break;
            }
        }

        return max(0, round($tax - $rebate, 2));
    }

    public static function monthlyTax(float $monthlyTaxableIncome, float $monthlyRebate = 0): float
    {
        $annual = $monthlyTaxableIncome * 12;
        $annualRebate = $monthlyRebate * 12;

        return round(self::annualTax($annual, $annualRebate) / 12, 2);
    }

    public static function monthlyTaxFromGross(float $monthlyGross, float $rebate = 0): float
    {
        return self::monthlyTax(max(0, $monthlyGross - $rebate), 0);
    }
}
