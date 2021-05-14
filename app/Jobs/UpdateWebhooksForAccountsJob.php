<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Services\PlaidServiceContract;
use App\Models\AccessToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWebhooksForAccountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(PlaidServiceContract $plaid): void
    {
        $page = 1;
        do {
            $paginator = AccessToken::paginate(50, ['*'], 'page', $page++);

            $items = $paginator->items();

            /** @var AccessToken $item */
            foreach ($items as $item) {
                $plaid->updateWebhook($item->token);
            }
        } while ($paginator->hasMorePages());
    }
}
