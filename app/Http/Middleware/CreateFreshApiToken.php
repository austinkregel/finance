<?php

namespace App\Http\Middleware;

use App\Http\Guards\AccessTokenGuard;
use App\Repositories\TokenRepository;
use Closure;
use Illuminate\Http\Response;

class CreateFreshApiToken
{
    /**
     * The token repository implementation.
     *
     * @var  TokenRepository
     */
    protected $tokens;

    /**
     * Create a new middleware instance.
     *
     * @param TokenRepository $tokens
     * @return void
     */
    public function __construct(TokenRepository $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);
        if ($this->shouldReceiveFreshToken($request, $response)) {
            $response->withCookie($this->tokens->createTokenCookie($request->user()));
        }

        return $response;
    }

    /**
     * Determine if the given request should receive a fresh token.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return bool
     */
    protected function shouldReceiveFreshToken($request, $response)
    {
        return $this->requestShouldReceiveFreshToken($request) && $this->responseShouldReceiveFreshToken($response);
    }

    /**
     * Determine if the request should receive a fresh token.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function requestShouldReceiveFreshToken($request)
    {
        return $request->isMethod('GET') && $request->user() && ! $request->ajax();
    }

    /**
     * Determine if the response should receive a fresh token.
     *
     * @param \Illuminate\Http\Response $response
     * @return bool
     */
    protected function responseShouldReceiveFreshToken($response)
    {
        return $response instanceof Response && ! $this->alreadyContainsToken($response);
    }

    /**
     * Determine if the given response already contains a Spark API token.
     *
     * This avoids us overwriting a just "refreshed" token.
     *
     * @param \Illuminate\Http\Response $response
     * @return bool
     */
    protected function alreadyContainsToken($response)
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === AccessTokenGuard::COOKIE_NAME) {
                return true;
            }
        }

        return false;
    }
}
