<?php

namespace App\Contracts\Models;

use Illuminate\Contracts\Support\Arrayable;

interface ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool;
}
