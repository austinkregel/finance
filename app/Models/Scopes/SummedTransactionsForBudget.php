<?php

namespace App\Models\Scopes;

use App\Budget;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use RRule\RRule;

class SummedTransactionsForBudget implements Scope
{
    /**
     * @param  Builder  $builder
     * @param  Budget  $model
     */
    public function apply (Builder $builder, Model $model)
    {
    }
}