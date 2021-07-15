<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Services\PlaidServiceContract;
use App\Jobs\Traits\PlaidTryCatchErrorForToken;
use App\Models\AccessToken;
use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncPlaidAccountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PlaidTryCatchErrorForToken;

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
    public function handle(PlaidServiceContract $plaid): void
    {
        $response = $this->tryCatch(fn () => $plaid->getAccounts($this->accessToken->token), $this->accessToken);

        if (! $response) {
            return;
        }

        $accounts = $response['accounts'];

        $user = $this->accessToken->user;

        foreach ($accounts as $account) {
            /** @var Account $accountEloquent */
            $accountEloquent = Account::where('account_id', $account->account_id)->first();

            if (empty($accountEloquent)) {
                $user->accounts()->create([
                    'account_id' => $account->account_id,
                    'mask' => $account->mask,
                    'name' => $account->name,
                    'official_name' => $account->official_name ?? '',
                    'balance' => $account->balances->current ?? 0,
                    'available' => $account->balances->available ?? 0,
                    'subtype' => $account->subtype,
                    'type' => $account->type,
                    'access_token_id' => $this->accessToken->id,
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
                'type' => $account->type,
            ]);
        }
    }
}
