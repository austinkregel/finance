<?php

declare(strict_types=1);

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
        /** @var AccessToken $accessToken */
        foreach ($accessTokens as $accessToken) {
            $accessToken->log(
                sprintf(
                    'requested a historical sync for %s - %s',
                    $start->format('Y-m-d'),
                    $now->format('Y-m-d')
                )
            );
        }

        for ($i = 0; $i < $diff; $i++) {
            $startOfTheMonth = $now->copy()->startOfMonth()->subMonths($i)->startOfMonth();

            /** @var AccessToken $accessToken */
            foreach ($accessTokens as $accessToken) {
                dispatch(new SyncPlaidTransactionsJob($accessToken, $startOfTheMonth, $startOfTheMonth->copy()->endOfMonth(), false));
            }
        }
    }

    public function validate(): array
    {
        return [
            'access_tokens' => 'required|array',
            'date' => 'required|date',
        ];
    }
}
