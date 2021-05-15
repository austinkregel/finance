<?php
declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Budget;
use Carbon\Carbon;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    /**
     * @dataProvider dateDataProvider
     */
    public function testGetStartOfCurrentPeriod($interval, $freq, $startedAt, $startOfPeriod, $endOfPeriod): void
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
            [1, 'MONTHLY', Carbon::create(2020, 1, 1, 0, 0, 0), '2020-01-01', '2020-02-01'],
            [2, 'MONTHLY', Carbon::create(2020, 1, 1, 0, 0, 0), '2020-01-01', '2020-03-01'],
            [1, 'YEARLY', Carbon::create(2020, 1, 1, 0, 0, 0), '2020-01-01', '2021-01-01'],
            // Technically this is a new period since the test now is the 15th.
            [2, 'WEEKLY', Carbon::create(2020, 1, 1, 0, 0, 0), '2020-01-15', '2020-01-29'],
        ];
    }
}
