<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Budget;
use Illuminate\Http\Request;

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

    public function tags(Request $request, Budget $budget)
    {
        $budget->tags()->sync($request->all());

        return $this->json($budget->refresh());
    }
}
