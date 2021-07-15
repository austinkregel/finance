<?php
declare(strict_types=1);

namespace App\Http\Controllers\Plaid;

use App\Contracts\Services\PlaidServiceContract;
use App\Http\Controllers\Controller;
use App\Models\AccessToken;
use Illuminate\Http\Request;

class UpdateLinkTokenController extends Controller
{
    public function __invoke(Request $request, PlaidServiceContract $plaid)
    {
        $accessToken = AccessToken::find($request->get('access_token'));
        if (empty($accessToken)) {
            abort(404);
        }
        $updatedToken = $plaid->updateLinkToken($request->user()->id, $accessToken->token);
        $accessToken->should_sync = true;
        $accessToken->save();

        return $updatedToken;
    }
}
