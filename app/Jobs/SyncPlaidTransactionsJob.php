<?php

namespace App\Jobs;

use App\Contracts\Services\PlaidServiceContract;
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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AccessToken $access, Carbon $startDate, Carbon $endDate)
    {
        $this->accessToken = $access;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PlaidServiceContract $plaid, GenericRepository $repository)
    {
        $this->repository = $repository;
        $transactionsResponse = $plaid->getTransactions($this->accessToken->token, $this->startDate, $this->endDate);
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
            $localTransaction = Transaction::where(function ($query) use ($transaction) {
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
//                    'pending_transaction_id' => $transaction->pending_transaction_id,
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_type' => $transaction->transaction_type,
                ]);
            }

            $localTransaction->update([
                'account_id' => $transaction->account_id,
                'amount' => $transaction->amount,
                'category_id' => $transaction->category_id,
                'date' => Carbon::parse($transaction->date),
                'name' => $transaction->name,
                'pending' => $transaction->pending,
//                    'pending_transaction_id' => $transaction->pending_transaction_id,
                'transaction_id' => $transaction->transaction_id,
                'transaction_type' => $transaction->transaction_type,
            ]);

            $categories = $transaction->category ?? [];

            $localTransaction->categories()->sync([]);

            foreach ($categories as $category) {
                $localTransaction->categories()->sync([
                    $repository->find(Category::class, 'name', $category)->id
                ], false);
            }
        }
    }
}
