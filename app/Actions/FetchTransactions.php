<?php

namespace App\Actions;

use App\Jobs\SyncPlaidTransactionsJob;
use Carbon\Carbon;

class FetchTransactions extends Action
{
    public function handle(): void
    {
        $accessTokenId = request()->get('access_token_id');
        $accessToken = auth()->user()->accessTokens()->find($accessTokenId);

        if (empty($accessToken)) {
            return;
        }

        $months = request()->get('months');

        $startDate = Carbon::now()->startOfMonth()->subMonths($months);
        $endDate = Carbon::now()->endOfMonth();

        dispatch(new SyncPlaidTransactionsJob($accessToken, $startDate, $endDate));
    }

    public function validate(): array
    {
        return [
            'access_token_id' => 'required|exists:access_tokens,id',
            'months' => 'integer|max:12|min:1'
        ];
    }
}
