<?php

namespace App\Jobs;

use App\Contracts\Services\PlaidServiceContract;
use App\Models\AccessToken;
use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncPlaidAccountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Account
     */
    protected $accessToken;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PlaidServiceContract $plaid)
    {
        $response = $plaid->getAccounts($this->accessToken->token);

        $accounts = $response['accounts'];

        foreach ($accounts as $account) {
            /** @var Account $accountEloquent */
            $accountEloquent = Account::where('account_id', $account->account_id)->first();

            if (empty($accountEloquent)) {
                Account::create([
                    'account_id' => $account->account_id,
                    'mask' => $account->mask,
                    'name' => $account->name,
                    'official_name' => $account->official_name ?? '',
                    'balance' => $account->balances->current ?? 0,
                    'available' => $account->balances->available ?? 0,
                    'subtype' => $account->subtype,
                    'type' => $account->type,
                    'access_token_id' => $this->accessToken->id
                ]);

                continue;
            }

            $accountEloquent->update([
                'mask' => $account->mask,
                'name' => $account->name,
                'official_name' => $account->official_name ?? '',
                'balance' => $account->balances->current ?? 0,
                'available' => $account->balances->available ?? 0,
                'subtype' => $account->subtype,
                'type' => $account->type
            ]);
        }
    }
}
