<?php

namespace App\Contracts;

use App\Condition;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Condition[] $conditionals
 */
interface ConditionableContract
{
    public function conditionals(): MorphMany;
}
