<?php

namespace App\Http\Controllers\Api;

use App\FailedJob;

class FailedJobController extends AbstractResourceController
{
    public function __construct(FailedJob $model)
    {
        parent::__construct($model);
    }
}
