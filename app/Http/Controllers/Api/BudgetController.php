<?php

namespace App\Http\Controllers\Api;

use App\Budget;

class BudgetController extends AbstractResourceController
{
    public function __construct(Budget $model)
    {
        parent::__construct($model);
    }

    public function totalSpends(Budget $budget)
    {
        return $budget->findTotalSpends($budget->getStartOfCurrentPeriod());
    }
}
