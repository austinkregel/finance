<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\RemoveTransactionsJob;
use App\Jobs\SyncPlaidTransactionsJob;
use App\Models\Account;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __invoke(Request $request): void
    {
        switch (strtolower($request->get('webhook_type'))) {
            case 'transactions':
                $this->handleTransactionsWebhook($request);

                return;
            case 'assets':
            case 'auth':
            case 'holdings':
            case 'payment_initiation':
            case 'item':
        }
    }

    protected function handleTransactionsWebhook(Request $request): void
    {
        /** @var Account $account */
        $account = Account::with('token')->firstWhere('account_id', $request->get('item_id'));
        if ($account === null) {
            return;
        }

        switch (strtolower($request->get('webhook_code'))) {
            case 'transactions_removed':
                $job = new RemoveTransactionsJob($account, $request->get('removed_transactions', []));
                break;
            case 'initial_update':
            case 'default_update':
                $job = new SyncPlaidTransactionsJob($account->token, now()->subMonth(), now());
                break;
            case 'historical_update':
                $job = new SyncPlaidTransactionsJob($account->token, now()->subMonths(24), now(), false);
                break;
        }
        $this->dispatch($job);
    }
}
