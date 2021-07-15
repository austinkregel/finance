<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Activity;

class ActivitiesController extends AbstractResourceController
{
    public function __construct(Activity $model)
    {
        parent::__construct($model);
    }
}
