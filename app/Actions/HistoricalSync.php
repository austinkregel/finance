<?php

namespace App\Actions;

use App\Jobs\SyncPlaidTransactionsJob;
use App\Models\AccessToken;
use Carbon\Carbon;

class HistoricalSync extends Action
{
    public function handle(): void
    {
        $tokens = request()->get('access_tokens', []);

        $accessTokens = AccessToken::whereIn('id', $tokens)->where('user_id', auth()->id())->get();

        $now = now();
        $start = Carbon::parse(request()->get('date'));
        /** @var int $diff */
        $diff = $now->diffInMonths($start);

        for ($i = 0; $i < $diff; $i ++) {
            $dateChunk = $now->copy()->subMonths($i)->startOfMonth();

            /** @var AccessToken $accessToken */
            foreach ($accessTokens as $accessToken) {
                dispatch(new SyncPlaidTransactionsJob($accessToken, $dateChunk, $dateChunk->copy()->endOfMonth(), false));
            }
        }
    }

    public function validate(): array
    {
        return [
            'access_tokens' => 'required|array',
            'date' => 'required|date'
        ];
    }
}
