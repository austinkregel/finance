<?php

namespace App\Models\Traits;

use App\Condition;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Condition[] $conditionals
 */
trait Conditionable
{
    public function conditionals(): MorphMany
    {
        return $this->morphMany(Condition::class, 'conditionable');
    }
}
