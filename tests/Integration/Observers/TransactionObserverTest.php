<?php

namespace Tests\Integration\Observers;

use App\BillName;
use App\Models\Transaction;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionObserverTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function testSubscriptionCreated()
    {
        Carbon::setTestNow(Carbon::create(2019, 1, 2, 0, 0, 0));

        $this->assertEmpty(BillName::all());
        $this->assertEmpty(Subscription::all());
        $transaction = new Transaction;
        $transaction->fill([
            'account_id' => 'fake data',
            'amount' => '381.30',
            'date' => Carbon::create(2018, 2, 4),
            'name' => 'fake transactional data',
            'pending' => 'fake data',
            'transaction_id' => 'fake data',
            'transaction_type' => 'fake data',
            'category_id' => 'fake data',
        ])->save();

        $this->assertEmpty(BillName::all());
        $this->assertEmpty(Subscription::all());

        $transaction->fill([
            'is_subscription' => true,
        ])->save();

        $this->assertNotEmpty(BillName::all());
        $this->assertNotEmpty(Subscription::all());


        $this->assertDatabaseHas('bill_names', [
            'name' => 'fake transactional data',
            'type' => 'subscription',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'name' => 'fake transactional data',
            'type' => 'subscription',
            'amount' => 381.30,
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => '2018-02-04 00:00:00',
        ]);
     }
}
