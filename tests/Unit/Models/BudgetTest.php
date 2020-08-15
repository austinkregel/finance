<?php

namespace Tests\Unit\Models;

use App\Budget;
use Carbon\Carbon;
use Tests\TestCase;

class BudgetTest extends  TestCase
{
    public function testGetStartOfCurrentPeriod()
    {
        Carbon::setTestNow(
            Carbon::create(2020, 1, 15, 0, 0, 0)
        );
        $budget = new Budget([
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => Carbon::create(2020, 1, 11, 0, 0, 0),
        ]);

        $this->assertSame('2020-01-01', $budget->getStartOfCurrentPeriod()->format('Y-m-d'));
        $this->assertSame('2020-01-31', $budget->getEndOfCurrentPeriod()->format('Y-m-d'));
    }
}