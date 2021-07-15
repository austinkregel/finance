<?php
declare(strict_types=1);

namespace App\Http\Controllers\Plaid;

use App\Contracts\Services\PlaidServiceContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateLinkTokenController extends Controller
{
    public function __invoke(Request $request, PlaidServiceContract $plaid)
    {
        return $plaid->createLinkToken((string) $request->user()->id);
    }
}
