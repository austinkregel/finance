<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;

class CategoryController extends AbstractResourceController
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}