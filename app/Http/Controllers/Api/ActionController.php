<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Action;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(Action $action, Request $request)
    {
        $this->validate($request, $action->validate());

        $action->handle();

        return $this->json('');
    }
}
