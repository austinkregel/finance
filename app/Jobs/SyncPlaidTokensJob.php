<?php


namespace App\Jobs;


use App\Contracts\Services\PlaidServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncPlaidTokensJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        /** @var PlaidServiceContract $plaidService */
        $plaidService = app(PlaidServiceContract::class);
        foreach (\App\Models\AccessToken::all() as $token) {
            try {
                $response = $plaidService->getAccounts($token->token);
            } catch (\Exception $exception) {
                continue;
            }
            if (array_key_exists('message', $response)) {
                continue;
            }
            $accounts = $response['accounts'];

            foreach($accounts as $account) {
                $this->accounts[$account->account_id] = [
                    $account->account_id,
                    $account->mask,
                    $account->name,
                    $account->official_name,
                    $account->balances->current ?? 0,
                    $account->balances->available ?? 0,
                    $account->subtype,
                    $account->type
                ];
                $account->access_token_id = $token->id;
                event(new \App\Events\UpdateAccountEvent(new \App\Models\Domain\Account($account), $token->user));
            }
        }
    }
}
