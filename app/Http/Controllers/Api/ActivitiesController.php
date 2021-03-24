<?php

namespace App\Http\Controllers\Api;

use App\Activity;

class ActivitiesController extends AbstractResourceController
{
    public function __construct(Activity $model)
    {
        parent::__construct($model);
    }
}
