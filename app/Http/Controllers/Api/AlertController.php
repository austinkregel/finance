<?php

namespace App\Http\Controllers\Api;

use App\Models\Alert;

class AlertController extends AbstractResourceController
{
    public function __construct(Alert $model)
    {
        parent::__construct($model);
    }
}
