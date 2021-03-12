<?php

namespace Tests\Integration\Listeners;

use App\Budget;
use App\Events\BudgetBreachedEstablishedAmount;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Alert;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Tests\TestCase;

class TriggerAlertForBreachedBudget extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2021, 1, 15));
    }

    public function testHandleWithNoConditionalsCreatesAlert(): void
    {
        $user = factory(User::class)->create();
        /** @var Transaction $transaction */
        $transaction = factory(Transaction::class)->create([
            'account_id' => factory(Account::class)->create([
                'access_token_id' => factory(AccessToken::class)->create([
                    'user_id' => $user->id,
                ])->id,
            ])->account_id,
            'amount' => 100,
            'name' => 'Actual Transaction'
        ]);

        /** @var Alert $alert */
        $alert = $user->alerts()->create([
            'name' => 'Alert',
            'title' => '{{ transaction.name }} charged ${{ transaction.amount }}',
            'body' => 'Yo dog your budget breached {{budget.name}}',
            'payload' => '{}',
            'channels' => [DatabaseChannel::class],
            'webhook_url' => null,
            'messaging_service_channel' => null,
            'events' => [BudgetBreachedEstablishedAmount::class],
        ]);

        $budget = factory(Budget::class)->create([
            'amount' => 50,
            'started_at' => Carbon::create(2021, 1, 1),
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'user_id' => $user->id,
            'name' => 'Budget Breacher'
        ]);

        $event = unserialize(serialize(new BudgetBreachedEstablishedAmount($budget, $transaction)));
        $listener = new \App\Listeners\TriggerAlertForBreachedBudget();

        self::assertEmpty(\DB::table('notifications')->get()->all());
        $listener->handle($event);
        self::assertNotEmpty($notifications = \DB::table('notifications')->get()->all());
        // Ensure that our mustache service will format a templated title and body
        self::assertSame('Actual Transaction charged $100', json_decode(collect($notifications)->first()->data)->title);
        self::assertSame('Yo dog your budget breached Budget Breacher', json_decode(collect($notifications)->first()->data)->body);
    }
}
