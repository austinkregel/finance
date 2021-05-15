<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Guards\AccessTokenGuard;
use Carbon\Carbon;
use Firebase\JWT\JWT;

class TokenRepository
{
    public function createTokenCookie($user)
    {
        $token = JWT::encode([
            'sub' => $user->id,
            'xsrf' => csrf_token(),
            'expiry' => Carbon::now()->addMinutes(30)->getTimestamp(),
        ], config('app.key'));

        return cookie(
            AccessTokenGuard::COOKIE_NAME,
            $token,
            30,
            null,
            config('session.domain'),
            config('session.secure'),
            true
        );
    }
}
