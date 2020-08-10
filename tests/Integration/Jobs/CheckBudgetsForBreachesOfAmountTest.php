<?php

namespace Tests\Integration\Jobs;

use App\Budget;
use App\Events\BudgetBreachedEstablishedAmount;
use App\Jobs\CheckBudgetsForBreachesOfAmount;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckBudgetsForBreachesOfAmountTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleNoBudgets()
    {
        $job = new CheckBudgetsForBreachesOfAmount;

        $job->handle();

        $this->doesntExpectEvents([
            BudgetBreachedEstablishedAmount::class
        ]);
    }

    public function testHandleBudgets()
    {
        Carbon::setTestNow($now = Carbon::create(2020, 1, 1, 0, 0, 0));

        $user = factory(User::class)->create();

        $token = $user->accessTokens()->create([
            'token' => Str::random(16),
            'should_sync' => true,
        ]);

        $account = $token->accounts()->create(factory(Account::class)->make()->toArray());

        factory(Budget::class)->create([
            'name' => 'Fake budget',
            'amount' => 100,
            'frequency' => 1,
            'interval' => 'MONTHLY',
            'started_at' => $now,
            'count' => 1,
        ]);

        $budget = factory(Budget::class)->create([
            'name' => 'food',
            'amount' => 10,
            'frequency' => 1,
            'interval' => 'MONTHLY',
            'started_at' => $now,
            'count' => 1,
        ]);

        $category = factory(Category::class)->create();

        factory(Budget::class)->create([
            'name' => 'This other budget',
            'amount' => 100,
            'frequency' => 1,
            'interval' => 'MONTHLY',
            'started_at' => $now,
            'count' => 1,
            'user_id' => $user->id,
        ]);

        $tag = factory(Tag::class)->create();

        $budget->tags()->sync([$tag->id]);

        $transaction = Transaction::create([
            'name' => 'Subway',
            'amount' => 15,
            'account_id' => $account->account_id,
            'date' => $now->addDay(),
            'pending' => false,
            'category_id' => $category->category_id,
            'transaction_id' => Str::random(32),
            'transaction_type' => 'special',
        ]);

        $transaction->tags()->sync([$tag->id]);
        Carbon::setTestNow($now = Carbon::create(2020, 1, 15, 0, 0, 0));

        $job = new CheckBudgetsForBreachesOfAmount;

        $job->handle();

        $this->doesntExpectEvents([
            BudgetBreachedEstablishedAmount::class
        ]);
    }
}