<?php

namespace App\Jobs;

use App\Contracts\Services\PlaidServiceContract;
use App\Events\TransactionCreated;
use App\Jobs\Traits\PlaidTryCatchErrorForToken;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Kregel\LaravelAbstract\Repositories\GenericRepository;

class SyncPlaidTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PlaidTryCatchErrorForToken;

    /**
     * @var Account
     */
    private $accessToken;

    /**
     * @var Carbon
     */
    private $startDate;

    /**
     * @var Carbon
     */
    private $endDate;

    private $repository;

    protected $shouldSendAlerts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AccessToken $access, Carbon $startDate, Carbon $endDate, ?bool $shouldSendAlerts = true)
    {
        $this->accessToken = $access;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->shouldSendAlerts = $shouldSendAlerts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PlaidServiceContract $plaid, GenericRepository $repository): void
    {
        $this->repository = $repository;

        if ($this->attempts() > 1) {
            sleep(5);
        }

        $transactionsResponse = $this->tryCatch(fn () => $plaid->getTransactions($this->accessToken->token, $this->startDate, $this->endDate), $this->accessToken);

        if (!$transactionsResponse) {
            return;
        }

        $transactions = $transactionsResponse->get('transactions');
        info(
            sprintf(
                'Found %d transactions for date range [%s] - [%s] on account %s',
                count($transactions),
                $this->startDate->format('Y-m-d'),
                $this->endDate->format('Y-m-d'),
                $this->accessToken->accounts->map->name->join(', ')
            )
        );

        foreach ($transactions as $transaction) {
            /** @var Transaction $localTransaction */
            $localTransaction = Transaction::where(function ($query) use ($transaction): void {
                $query->where('transaction_id', $transaction->transaction_id);

                if ($transaction->pending_transaction_id) {
                    $query->orWhere('transaction_id', $transaction->pending_transaction_id);
                }
            })->first();

            if (empty($localTransaction)) {
                $localTransaction = $repository->findOrCreate(Transaction::class, 'transaction_id', $transaction->transaction_id, [
                    'account_id' => $transaction->account_id,
                    'amount' => $transaction->amount,
                    'category_id' => $transaction->category_id,
                    'date' => Carbon::parse($transaction->date),
                    'name' => $transaction->name,
                    'pending' => $transaction->pending,
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_type' => $transaction->transaction_type,
                ]);
            } else {
                $localTransaction->update([
                    'account_id' => $transaction->account_id,
                    'amount' => $transaction->amount,
                    'category_id' => $transaction->category_id,
                    'date' => Carbon::parse($transaction->date),
                    'name' => $transaction->name,
                    'pending' => $transaction->pending,
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_type' => $transaction->transaction_type,
                ]);
            }

            $this->syncTransactions($transaction, $localTransaction);
            event(new TransactionCreated($localTransaction, $this->shouldSendAlerts));
        }
    }

    protected function syncTransactions($transaction, $localTransaction): void
    {
        $categoriesToSync = [];
        foreach ($transaction->category as $category) {
            $categoriesToSync[] = cache()->remember('category.'.$category, now()->addHour(), fn () => Category::where('name', $category)->first())->id;
        }

        $localTransaction->categories()->sync($categoriesToSync);
    }
}
