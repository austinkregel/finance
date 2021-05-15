<?php
declare(strict_types=1);

namespace App\Actions;

use App\Events\RegroupEvent;
use App\Models\Account;
use App\Models\Transaction;

class EmitRefreshEvent extends Action
{
    public function handle(): void
    {
        $userId = auth()->id();
        $page = 1;

        $accounts = Account::whereHas('users', function ($query) use ($userId): void {
            $query->where('user_id', $userId);
        })->get();

        foreach ($accounts as $account) {
            do {
                $pagination = Transaction::where('account_id', $account->id)->paginate(50, ['*'], 'page', $page++);

                foreach ($pagination->items() as $transaction) {
                    event(new RegroupEvent($transaction));
                }
            } while ($pagination->hasMorePages());
        }
    }

    public function validate(): array
    {
        return [];
    }
}
