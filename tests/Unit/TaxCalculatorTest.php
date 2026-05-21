<?php

namespace Tests\Unit;

use App\Services\Payroll\TaxCalculator;
use PHPUnit\Framework\TestCase;

class TaxCalculatorTest extends TestCase
{
    public function test_zero_tax_below_exempt_slab(): void
    {
        $this->assertEquals(0, TaxCalculator::monthlyTaxFromGross(25000));
    }

    public function test_positive_tax_above_exempt_slab(): void
    {
        $monthly = TaxCalculator::monthlyTaxFromGross(50000);
        $this->assertGreaterThan(0, $monthly);
    }

    public function test_annual_tax_matches_monthly_times_twelve(): void
    {
        $monthlyGross = 60000;
        $annualTax = TaxCalculator::annualTax($monthlyGross * 12);
        $monthlyTax = TaxCalculator::monthlyTaxFromGross($monthlyGross);
        $this->assertEqualsWithDelta($annualTax, $monthlyTax * 12, 1.0);
    }
}
