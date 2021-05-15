<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Account;

class AccountController extends AbstractResourceController
{
    public function __construct(Account $model)
    {
        parent::__construct($model);
    }
}
