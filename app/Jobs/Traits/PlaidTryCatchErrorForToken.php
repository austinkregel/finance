<?php

namespace App\Jobs\Traits;

trait PlaidTryCatchErrorForToken
{
    public function tryCatch(callable $callable, $accessToken)
    {
        try {
            $response = $callable();
            $accessToken->should_sync = true;
            $accessToken->save();

            return $response;
        } catch (\Throwable $exception) {
            $accessToken->should_sync = false;
            $accessToken->error .= "\n".now()->format('Y-m-d H:i:s').' ---- '.$exception->getMessage();
            $accessToken->save();

            return false;
        }
    }
}
