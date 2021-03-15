<?php

namespace App\Jobs;

use App\Contracts\Services\PlaidServiceContract;
use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWebhooksForAccountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(PlaidServiceContract $plaid)
    {
        $page = 1;
        do {
            $paginator = Account::paginate(50, ['*'], 'page', $page++);

            $items = $paginator->items();

            /** @var Account $item */
            foreach ($items as $item) {
                $plaid->updateWebhook($item->account_id);
            }
        } while ($paginator->hasMorePages());
    }
}
