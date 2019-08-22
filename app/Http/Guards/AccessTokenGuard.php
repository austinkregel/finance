<?php

namespace App\Http\Guards;

use App\Models\AccessToken;
use Exception;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TokenRepository;

class AccessTokenGuard
{
    public const COOKIE_NAME = 'auth_token';

    /**
     * The AccessToken repository implementation.
     *
     * @var TokenRepository
     */
    protected $tokens;

    /**
     * Create a new AccessToken guard instance.
     *
     * @param  TokenRepository  $tokens
     * @return void
     */
    public function __construct(TokenRepository $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Get the authenticated user for the given request.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(Request $request)
    {
        if (Auth::user()) {
            return $this->alreadyHasUser();
        }

        /** @var AccessToken $token */
        if (! $token = $this->getToken($request)) {
            return;
        }

        // If the AccessToken is valid we will return the user instance that is associated with
        // the AccessToken as well as populate the AccessToken usage time. If a AccessToken wasn't found
        // of course this method will return null and no user will be authenticated.
        Auth::setDefaultDriver('api');

        $token->touchLastUsedTimestamp();

        return $token->user->setToken($token);
    }

    /**
     * Return the current user with a fresh transient AccessToken.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    protected function alreadyHasUser()
    {
        return Auth::user()->setToken(
            $this->createTransientToken(Auth::id(), Carbon::now()->addMinutes(5))
        );
    }

    /**
     * Get the AccessToken instance from the database.
     *
     * @param  Request  $request
     * @return AccessToken
     */
    protected function getToken(Request $request)
    {
        $token = $this->getTokenFromRequest($request);

        if ($token instanceof AccessToken) {
            return $token;
        } else {
            return $token ? $this->tokens->validToken($token) : null;
        }
    }

    /**
     * Get the AccessToken for the given request.
     *
     * @param  Request  $request
     * @return AccessToken|string
     */
    protected function getTokenFromRequest(Request $request)
    {
        $bearer = $request->bearerToken();

        // First we will check to see if the AccessToken is in the request input data or is a bearer
        // AccessToken on the request. If it is, we will consider this the AccessToken, otherwise we'll
        // look for the AccessToken in the cookies then attempt to validate that it is correct.
        if ($token = $request->input('api_token', $bearer)) {
            return $token;
        }

        if ($request->cookie(static::COOKIE_NAME)) {
            return $this->getTokenFromCookie($request);
        }
    }

    /**
     * Get the AccessToken for the given request cookie.
     *
     * @param  Request  $request
     * @return AccessToken
     */
    protected function getTokenFromCookie($request)
    {
        // If we need to retrieve the AccessToken from the cookie, it'll be encrypted so we must
        // first decrypt the cookie and then attempt to find the AccessToken value within the
        // database. If we can't decrypt the value we'll bail out with a null return.
        try {
            $token = JWT::decode(decrypt($request->cookie(static::COOKIE_NAME)), config('app.key'));
        } catch (Exception $e) {
            return;
        }

        // We will compare the XSRF AccessToken in the decoded API AccessToken against the XSRF header
        // sent with the request. If the two don't match then this request is sent from
        // a valid source and we won't authenticate the request for further handling.
        if (! $this->validXsrf($token, $request)) {
            return;
        }

        // Here we will create a AccessToken instance from the JWT AccessToken. This'll be a transient
        // AccessToken which allows all operations since the user is physically logged into a
        // screen of the application. We'll check the expiration date then return it.
        $token = $this->createTransientToken(
            $token['sub'], Carbon::createFromTimestamp($token['expiry'])
        );

        return $token->isExpired() ? null : $token;
    }

    /**
     * Create a new transient AccessToken instance for the given user.
     *
     * @param  int  $userId
     * @param  Carbon  $expiration
     * @return AccessToken
     */
    protected function createTransientToken($userId, Carbon $expiration)
    {
        return (new AccessToken)->forceFill([
            'user_id' => $userId,
            'transient' => true,
            'expires_at' => $expiration,
        ]);
    }

    /**
     * Determine if the XSRF / header are valid and match.
     *
     * @param  array  $token
     * @param  Request  $request
     * @return bool
     */
    protected function validXsrf($token, $request)
    {
        return isset($token['xsrf']) && hash_equals(
            $token['xsrf'], (string) $this->decryptXsrfHeader($request)
        );
    }

    /**
     * Decrypt the XSRF header on the given request.
     *
     * @param  Request  $request
     * @return string|null
     */
    protected function decryptXsrfHeader($request)
    {
        try {
            return decrypt($request->header('X-XSRF-TOKEN'));
        } catch (Exception $e) {
        }
    }
}
