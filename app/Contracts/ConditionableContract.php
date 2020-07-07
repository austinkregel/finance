<?php

namespace App\Contracts;

use App\Condition;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Condition[] $conditionals
 * @property boolean $must_all_conditions_pass
 */
interface ConditionableContract
{
    public function conditionals(): MorphMany;
}
