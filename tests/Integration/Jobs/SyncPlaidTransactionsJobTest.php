<?php

namespace Tests\Integration\Jobs;

use App\Contracts\Services\PlaidServiceContract;
use App\Events\TransactionCreated;
use App\Events\TransactionUpdated;
use App\Jobs\SyncPlaidTransactionsJob;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Kregel\LaravelAbstract\Repositories\GenericRepository;
use Tests\TestCase;
use Mockery;

class SyncPlaidTransactionsJobTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleNoTransactions(): void
    {
        $this->assertDatabaseCount('activity_log', 0);
        $token = factory(AccessToken::class)->create();

        $job = new SyncPlaidTransactionsJob(
            $token,
            $start = now(),
            $end = now(),
            true
        );

        $plaidService = Mockery::mock(PlaidServiceContract::class);

        $plaidService->shouldReceive('getTransactions')
            ->once()
            ->with($token->token, $start, $end)
            ->andReturn(collect([
                'transactions' => [],
                'accounts' => []
            ]));

        $job->handle($plaidService, new GenericRepository);
        $this->assertDatabaseCount('activity_log', 0);
    }

    public function testHandleInvalidResponse(): void
    {
        $this->assertDatabaseCount('activity_log', 0);
        $this->expectException(InvalidArgumentException::class);
        $token = factory(AccessToken::class)->create();

        $job = new SyncPlaidTransactionsJob(
            $token,
            $start = now(),
            $end = now(),
            true
        );

        $plaidService = Mockery::mock(PlaidServiceContract::class);

        $plaidService->shouldReceive('getTransactions')
            ->once()
            ->with($token->token, $start, $end)
            ->andThrow(new InvalidArgumentException('Exception Message'));

        try {
            $job->handle($plaidService, new GenericRepository);
        } finally {
            $accessToken = AccessToken::find($token->id);
            $this->assertSame(false, $accessToken->should_sync, 'Account syncing should be disabled when a failure occurs.');

            $this->assertDatabaseCount('activity_log', 1);
            $this->assertDatabaseHas('activity_log', [
                'log_name' => 'activity',
                'subject_id' => $accessToken->id,
                'description' => 'Exception Message'
            ]);
        }
    }

    public function testHandleWillCreateNewTransactions(): void
    {
        Carbon::setTestNow(Carbon::create(2021, 1, 1));
        $token = factory(AccessToken::class)->create();
        $this->assertDatabaseCount('transactions', 0);

        $this->expectsEvents([
            TransactionCreated::class,
        ]);
        $job = new SyncPlaidTransactionsJob(
            $token,
            $start = Carbon::create(2020, 12, 1),
            $end = Carbon::create(2020, 12, 31),
            true
        );

        $plaidService = Mockery::mock(PlaidServiceContract::class);

        $plaidService->shouldReceive('getTransactions')
            ->once()
            ->with($token->token, $start, $end)
            ->andReturn(collect([
                'transactions' => [
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 2), false),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 4), true),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 8), false),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 10), true),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 11), false),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 11), true),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 22), false),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 30), false),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 31), false),
                ]
            ]));

        $job->handle($plaidService, new GenericRepository);

        $this->assertDatabaseCount('transactions', 9);
    }

    public function testHandleWillUpdateExistingTransactions(): void
    {
        Carbon::setTestNow(Carbon::create(2021, 1, 1));
        $token = factory(AccessToken::class)->create();
        $this->assertDatabaseCount('transactions', 0);

        $this->expectsEvents([
            TransactionCreated::class,
            TransactionUpdated::class,
        ]);

        $job = new SyncPlaidTransactionsJob(
            $token,
            $start = Carbon::create(2020, 12, 1),
            $end = Carbon::create(2020, 12, 31),
            true
        );

        $plaidService = Mockery::mock(PlaidServiceContract::class);
        $transaction1 = $this->generatePlaidTransaction(Carbon::create(2020, 12, 8), false);

        $account = factory(Account::class)->create([
            'account_id' => $transaction1->account_id
        ]);

        $transaction2 = $this->generatePlaidTransaction(Carbon::create(2020, 12, 11), true);

        $pendingTransaction = $this->generatePlaidTransaction(Carbon::create(2020, 12, 11), false);

        $pendingTransaction->pending_transaction_id = str_shuffle('lPNjeW1nR6CDn5okmGQ6hEpMo4lLNoSrzqDje');

        $storedPendingTransaction = Transaction::create([
            'account_id' => $pendingTransaction->account_id,
            'amount' => 90,
            'category_id' => $pendingTransaction->category_id,
            'date' => Carbon::parse($pendingTransaction->date),
            'name' => $pendingTransaction->name,
            'pending' => true,
            'transaction_id' => $pendingTransaction->pending_transaction_id,
            'transaction_type' => $pendingTransaction->transaction_type,
            'pending_transaction_id' => null,
            'data' => $pendingTransaction,
        ]);
        $storedTransaction1 = Transaction::create([
            'account_id' => $transaction1->account_id,
            'amount' => 0,
            'category_id' => $transaction1->category_id,
            'date' => Carbon::parse($transaction1->date),
            'name' => $transaction1->name,
            'pending' => $transaction1->pending,
            'transaction_id' => $transaction1->transaction_id,
            'transaction_type' => $transaction1->transaction_type,
            'pending_transaction_id' => $transaction1->pending_transaction_id,
            'data' => $transaction1,
        ]);
        $storedTransaction2 = Transaction::create([
            'account_id' => $transaction2->account_id,
            'amount' => 0,
            'category_id' => $transaction2->category_id,
            'date' => Carbon::parse($transaction2->date),
            'name' => $transaction2->name,
            'pending' => $transaction2->pending,
            'transaction_id' => $transaction2->transaction_id,
            'transaction_type' => $transaction2->transaction_type,
            'pending_transaction_id' => $transaction2->pending_transaction_id,
            'data' => $transaction2,
        ]);
        $plaidService->shouldReceive('getTransactions')
            ->once()
            ->with($token->token, $start, $end)
            ->andReturn(collect([
                'transactions' => [
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 2), false),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 4), true),
                    $transaction1,
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 10), true),
                    $pendingTransaction,
                    $transaction2,
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 22), false),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 30), false),
                    $this->generatePlaidTransaction(Carbon::create(2020, 12, 31), false),
                ]
            ]));

        $job->handle($plaidService, new GenericRepository);

        $storedTransaction1->refresh();
        $storedTransaction2->refresh();

        $this->assertSame($storedTransaction1->amount, $transaction1->amount);
        $this->assertSame($storedTransaction2->amount, $transaction2->amount);

        $this->assertDatabaseMissing('transactions', [
            'amount' => $pendingTransaction->amount,
            'transaction_id' => $pendingTransaction->pending_transaction_id,
            'pending' => true,
            'name' => $pendingTransaction->name,
        ]);
        $this->assertDatabaseHas('transactions', [
            'amount' => $pendingTransaction->amount,
            'transaction_id' => $pendingTransaction->transaction_id,
            'pending_transaction_id' => $pendingTransaction->pending_transaction_id,
            'pending' => false,
            'name' => $pendingTransaction->name,
        ]);

        $this->assertDatabaseCount('transactions', 9);
    }

    protected function generatePlaidTransaction(Carbon $date, bool $hasLocation)
    {
        $faker = Factory::create();

        $category = cache()->remember('category.Shops', now()->addHour(), fn () => factory(Category::class)->create([
            'name' => 'Shops',
        ]));
        $category2 = cache()->remember('category.Computers and Electronics', now()->addHour(), fn () => factory(Category::class)->create([
            'name' => 'Computers and Electronics',
        ]));

        return (object) [
            'account_id' => 'BxBXxLj1m4HMXBm9WZZmCWVbPjX16EHwv99vp',
            'amount' => (float) mt_rand(5, 3000),
            'iso_currency_code' => 'USD',
            'unofficial_currency_code' => null,
            'category' => [$category->name, $category2->name],
            'category_id' => '19013000',
            'date' => $date->addDay()->format('Y-m-d'),
            'authorized_date' => $date->format('Y-m-d'),
            'location' => $hasLocation ? (object) [
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'region' => $faker->state,
                'postal_code' => $faker->postcode,
                'country' => 'US',
                'lat' => $faker->latitude,
                'lon' => $faker->longitude,
                'store_number' => $faker->randomNumber(5)
            ] : (object) [
                'address' => null,
                'city' => null,
                'region' => null,
                'postal_code' => null,
                'country' => null,
                'lat' => null,
                'lon' => null,
                'store_number' => null
            ],
            'name' => $faker->sentence,
            'merchant_name' => $faker->company,
            'payment_meta' => (object) [
                'by_order_of' => null,
                'payee' => null,
                'payer' => null,
                'payment_method' => null,
                'payment_processor' => null,
                'ppd_id' => null,
                'reason' => null,
                'reference_number' => null
            ],
            'payment_channel' => 'in store',
            'pending' => false,
            'pending_transaction_id' => null,
            'account_owner' => null,
            'transaction_id' => str_shuffle('lPNjeW1nR6CDn5okmGQ6hEpMo4lLNoSrzqDje'),
            'transaction_code' => null,
            'transaction_type' => 'place'
        ];
    }
}
