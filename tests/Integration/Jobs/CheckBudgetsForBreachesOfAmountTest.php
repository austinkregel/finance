<?php
declare(strict_types=1);

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

    public function testHandleNoBudgets(): void
    {
        $job = new CheckBudgetsForBreachesOfAmount();

        $job->handle();

        $this->doesntExpectEvents([
            BudgetBreachedEstablishedAmount::class,
        ]);
    }

    public function testHandleBudgets(): void
    {
        Carbon::setTestNow($now = Carbon::create(2020, 1, 1, 0, 0, 0));

        $user = User::factory()->create([
            'id' => 1,
        ]);

        $token = $user->accessTokens()->create([
            'token' => Str::random(16),
            'should_sync' => true,
        ]);

        $account = $token->accounts()->create(Account::factory()->make()->toArray());

        Budget::factory()->create([
            'name' => 'Fake budget',
            'amount' => 100,
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => $now,
            'count' => 1,
            'user_id' => 1,
        ]);

        $budget = Budget::factory()->create([
            'name' => 'food',
            'amount' => 10,
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => $now,
            'count' => 1,
            'user_id' => 1,
        ]);

        $category = Category::factory()->create();

        Budget::factory()->create([
            'name' => 'This other budget',
            'amount' => 100,
            'frequency' => 'MONTHLY',
            'interval' => 1,
            'started_at' => $now,
            'count' => 1,
            'user_id' => 1,
        ]);

        $tag = Tag::factory()->create();

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

        $job = new CheckBudgetsForBreachesOfAmount();

        $job->handle();

        $this->doesntExpectEvents([
            BudgetBreachedEstablishedAmount::class,
        ]);
    }
}
