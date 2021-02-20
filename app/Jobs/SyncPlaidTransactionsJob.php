<?php

namespace App\Jobs;

use App\Contracts\Services\PlaidServiceContract;
use App\Events\TransactionCreated;
use App\Events\TransactionUpdated;
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
        if ($this->attempts() > 1) {
            sleep(5);
        }

        info('Syncing for dates', [
            'end' => $this->endDate,
            'start' => $this->startDate,
            'token' => $this->accessToken->id
        ]);

        $transactionsResponse = $this->tryCatch(fn () => $plaid->getTransactions($this->accessToken->token, $this->startDate, $this->endDate), $this->accessToken);

        $transactions = $transactionsResponse->get('transactions');

        foreach ($transactions as $transaction) {
            $localTransactions = Transaction::where(function ($query) use ($transaction): void {
                $query->where('transaction_id', $transaction->transaction_id);

                /**
                 * Due to how Plaid handles pending transactions, we need to delete the transaction with a pending transaction id,
                 * and then create a new transaction
                 * @see https://plaid.com/docs/transactions/transactions-data/#reconciling-transactions
                 */
                if ($transaction->pending_transaction_id) {
                    $query->orWhere('transaction_id', $transaction->pending_transaction_id);
                }
            })->get();

            $localTransactions->map(function ($localTransaction) use ($transaction, $repository): void {
                if ($transaction->pending_transaction_id === $localTransaction->transaction_id) {
                    $localTransaction->delete();
                    $localTransaction = null;
                }

                if (empty($localTransaction)) {
                    $localTransaction = $this->createLocalTransaction($repository, $transaction);
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
                        'pending_transaction_id' => $transaction->pending_transaction_id,
                        'data' => $transaction
                    ]);
                    event(new TransactionUpdated($localTransaction, $this->shouldSendAlerts));
                }

                $this->syncTransactions($transaction, $localTransaction);
            });

            if ($localTransactions->isEmpty()) {
                $this->createLocalTransaction($repository, $transaction);
            }
        }
    }

    protected function syncTransactions($transaction, $localTransaction): void
    {
        $categoriesToSync = [];
        $categories = $transaction->category ?? [];
        foreach ($categories as $category) {
            $categoriesToSync[] = cache()->remember('category.'.$category, now()->addHour(), fn () => Category::where('name', $category)->first())->id;
        }

        $localTransaction->categories()->sync($categoriesToSync);
    }

    protected function createLocalTransaction($repository, $transaction)
    {
        $localTransaction = $repository->findOrCreate(Transaction::class, 'transaction_id', $transaction->transaction_id, [
            'account_id' => $transaction->account_id,
            'amount' => $transaction->amount,
            'category_id' => $transaction->category_id,
            'date' => Carbon::parse($transaction->date),
            'name' => $transaction->name,
            'pending' => $transaction->pending,
            'transaction_id' => $transaction->transaction_id,
            'transaction_type' => $transaction->transaction_type,
            'pending_transaction_id' => $transaction->pending_transaction_id,
            'data' => $transaction,
        ]);
        event(new TransactionCreated($localTransaction, $this->shouldSendAlerts));

        return $localTransaction;
    }
}
