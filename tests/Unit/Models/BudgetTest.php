<?php

namespace Tests\Unit\Models;

use App\Budget;
use Carbon\Carbon;
use Tests\TestCase;

class BudgetTest extends  TestCase
{
    /**
     * @dataProvider dateDataProvider
     */
    public function testGetStartOfCurrentPeriod($interval, $freq, $startedAt, $startOfPeriod, $endOfPeriod)
    {
        Carbon::setTestNow(
            Carbon::create(2020, 1, 15, 0, 0, 0)
        );
        $budget = new Budget([
            'frequency' => $freq,
            'interval' => $interval,
            'started_at' => $startedAt,
        ]);

        $this->assertSame($startOfPeriod, $budget->getStartOfCurrentPeriod()->format('Y-m-d'));
        $this->assertSame($endOfPeriod, $budget->getEndOfCurrentPeriod()->format('Y-m-d'));
    }

    public function dateDataProvider()
    {
        return [
            [1, 'MONTHLY', Carbon::create(2020, 1, 1, 0, 0, 0), '2020-01-01', '2020-01-31'],
            [2, 'MONTHLY', Carbon::create(2020, 1, 1, 0, 0, 0), '2020-01-01', '2020-02-29'],
            [1, 'YEARLY', Carbon::create(2020, 1, 1, 0, 0, 0), '2020-01-01', '2021-01-31'],
            [2, 'WEEKLY', Carbon::create(2020, 1, 1, 0, 0, 0), '2020-01-01', '2021-01-14'],
        ];
    }
}