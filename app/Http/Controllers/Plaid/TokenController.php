<?php
/**
 * TokenController
 */
declare(strict_types=1);

namespace App\Http\Controllers\Plaid;

use App\Http\Controllers\Controller;
use App\Services\Banking\PlaidService;
use App\User;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

/**
 * Class TokenController
 * @package App\Http\Controllers\Plaid
 */
class TokenController extends Controller
{
    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function __invoke(Request $request, LoggerInterface $logger)
    {
        $this->validate($request, [
            'institution' => 'required',
            'public_token' => 'required'
        ]);

        /** @var PlaidService $service */
        $service = app(PlaidService::class);
        $exchangedToken = $service->getAccessToken((string) $request->get('public_token'));

        $logger->info('Exchanged token [%s] for context', $exchangedToken);

        /** @var User $user */
        $user = auth()->user();

        $token = $user->accessTokens()->firstOrCreate([
            'token' => $exchangedToken['access_token'],
            'institution_id' => $request->get('institution'),
        ]);

        return $token;
    }
}
