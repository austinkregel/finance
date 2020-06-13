<?php

namespace App\Actions;

use App\Jobs\SyncPlaidAccountsJob;
use App\Models\AccessToken;

class RefreshAccountsFor extends Action
{
    public function handle(): void
    {
        $accessTokenId = request()->get('access_token_id');
        $accessToken = auth()->user()->accessTokens()->find($accessTokenId);

        if (empty($accessToken)) {
            return;
        }

        dispatch(new SyncPlaidAccountsJob($accessToken));
    }

    public function validate(): array
    {
        return [
            'access_token_id' => 'required|exists:access_tokens,id',
        ];
    }
}
