<?php
namespace App\Http\Controllers\Plaid;

use App\Contracts\Services\PlaidServiceContract;
use App\Http\Controllers\Controller;
use App\Services\Banking\PlaidService;
use App\User;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class CreateLinkTokenController extends Controller
{
    public function __invoke(Request $request, PlaidServiceContract $plaid)
    {
        $this->validate($request, []);

        return $plaid->createLinkToken($request->user()->id);
    }
}
