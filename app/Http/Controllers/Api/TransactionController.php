<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Transaction;

class TransactionController extends AbstractResourceController
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
}
