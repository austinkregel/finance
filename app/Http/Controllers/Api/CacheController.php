<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class CacheController
{
    public function __invoke(Request $request)
    {
        cache()->tags([$request->user()->email])->flush();

        return response('', 200);
    }
}
