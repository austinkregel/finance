<?php
declare(strict_types=1);

namespace App\Jobs\Traits;

use App\Models\AccessToken;

trait PlaidTryCatchErrorForToken
{
    public function tryCatch(callable $callable, AccessToken $accessToken)
    {
        try {
            $response = $callable();
            $accessToken->should_sync = true;
            $accessToken->save();

            return $response;
        } catch (\Throwable $exception) {
            $accessToken->log($exception->getMessage());
            $accessToken->should_sync = false;
            $accessToken->save();

            throw $exception;
        }
    }
}
