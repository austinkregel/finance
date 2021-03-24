<?php

namespace App\Http\Controllers\Api;

use App\Models\AccessToken;

class AccessTokenController extends AbstractResourceController
{
    public function __construct(AccessToken $model)
    {
        parent::__construct($model);
    }
}
