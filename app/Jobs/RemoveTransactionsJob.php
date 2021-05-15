<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Account $account;

    protected array $transactionIds;

    public function __construct(Account $account, array $transactionIds)
    {
        $this->account = $account;
        $this->transactionIds = $transactionIds;
    }

    public function handle(): void
    {
        $transactions = $this->account->transactions()
            ->whereIn('transaction_id', $this->transactionIds)
            ->get();

        $transactions->map->delete();
    }
}
