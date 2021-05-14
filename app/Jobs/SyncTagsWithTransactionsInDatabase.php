<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\RegroupEvent;
use App\Jobs\Traits\PlaidTryCatchErrorForToken;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Transaction;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncTagsWithTransactionsInDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PlaidTryCatchErrorForToken;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $page = 1;

        do {
            $transactions = Transaction::whereIn(
                'account_id',
                Account::whereIn(
                    'access_token_id',
                    AccessToken::where('user_id', $this->user->id)
                        ->pluck('id')
                )->pluck('account_id')
            )
                ->paginate(100, ['*'], 'page', $page++);

            foreach ($transactions as $transaction) {
                event(new RegroupEvent($transaction));
            }
        } while ($transactions->hasMorePages());
    }
}
