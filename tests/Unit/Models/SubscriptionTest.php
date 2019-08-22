<?php

namespace Tests\Unit\Models;

use App\Subscription;
use Carbon\Carbon;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    public function testIsDueToday()
    {
        Carbon::setTestNow(Carbon::create(2019, 1, 20));

        $subscription = new Subscription([
            'name' => 'Subscription',
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => now()->subMonth(),
        ]);

        $this->assertSame('2018-12-20', $subscription->started_at->format('Y-m-d'));
        $this->assertTrue($subscription->getIsDueTodayAttribute());
    }

    public function testIsDueTodayShouldReturnFalse()
    {
        Carbon::setTestNow(Carbon::create(2019, 1, 20));

        $subscription = new Subscription([
            'name' => 'Subscription',
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => now()->subDays(3),
        ]);

        $this->assertSame('2019-01-17', $subscription->started_at->format('Y-m-d'));
        $this->assertFalse($subscription->getIsDueTodayAttribute());
    }

    public function testIsDueAfterEndedAt()
    {
        Carbon::setTestNow(Carbon::create(2019, 1, 20));

        $subscription = new Subscription([
            'name' => 'Subscription',
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => now()->subMonths(3),
            'ended_at' => now()->subDays(3),
        ]);

        $this->assertSame('2018-10-20', $subscription->started_at->format('Y-m-d'));
        $this->assertFalse($subscription->getIsDueTodayAttribute());
    }

    public function testIsDueBeforeEndedAt()
    {
        Carbon::setTestNow(Carbon::create(2019, 1, 20));

        $subscription = new Subscription([
            'name' => 'Subscription',
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => now()->subMonths(3),
            'ended_at' => now()->addDays(3),
        ]);

        $this->assertSame('2018-10-20', $subscription->started_at->format('Y-m-d'));
        $this->assertTrue($subscription->getIsDueTodayAttribute());
    }

    public function testNextDueDate()
    {
        Carbon::setTestNow(Carbon::create(2019, 1, 20));

        $subscription = new Subscription([
            'name' => 'Subscription',
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => now()->subMonths(3),
        ]);

        $this->assertSame('2019-02-20', $subscription->getNextDueDateAttribute()->format('Y-m-d'));
    }

    public function testNextDueDateIsNull()
    {
        Carbon::setTestNow(Carbon::create(2019, 1, 20));

        $subscription = new Subscription([
            'name' => 'Subscription',
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => now()->subMonths(3),
            'ended_at' => now()->subDay(3),
        ]);

        $this->assertNull($subscription->getNextDueDateAttribute());
    }

    public function testNextDueDateIsNotWithAnEndedAt()
    {
        Carbon::setTestNow(Carbon::create(2019, 1, 20));

        $subscription = new Subscription([
            'name' => 'Subscription',
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => now()->subMonths(3),
            'ended_at' => now()->addDay(3),
        ]);

        $this->assertNull($subscription->getNextDueDateAttribute());
    }
}
