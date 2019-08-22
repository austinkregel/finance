<?php

namespace App\Observers;

use App\Jobs\SyncPlaidAccountsJob;
use App\Models\AccessToken;

class AccessTokenObserver
{
    /**
     * Handle the access token "created" event.
     *
     * @param  \App\Models\AccessToken  $accessToken
     * @return void
     */
    public function created(AccessToken $token)
    {
        SyncPlaidAccountsJob::dispatch($token);
    }
}
