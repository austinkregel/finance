<?php

namespace App\Services;

use App\Actions\Action;
use App\Actions\EmitRefreshEvent;
use App\Actions\FetchTransactions;
use App\Actions\HistoricalSync;
use App\Actions\RefreshAccountsFor;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActionService
{
    public const ACTIONS = [
        'refresh-accounts-for' => RefreshAccountsFor::class,
        'fetch-transactions' => FetchTransactions::class,
        'regroup-transactions' => EmitRefreshEvent::class,
        'historical-sync' => HistoricalSync::class
    ];

    public function build(?string $thing)
    {
        abort_if(empty($thing), 404);

        abort_unless(array_key_exists($thing, static::ACTIONS), 404);

        $action = static::ACTIONS[$thing];

        /** @var Action $actionInstance */
        return app($action);
    }

    public function validate(?string $thing, Request $request): array
    {
        abort_if(empty($thing), 404);

        abort_unless(array_key_exists($thing, static::ACTIONS), 404);

        /** @var Action $action */
        $action = static::ACTIONS[$thing];

        return $request->validate(app($action)->validate());
    }
}
