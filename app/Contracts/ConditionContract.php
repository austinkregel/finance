<?php

namespace App\Contracts\Models;

interface ConditionContract
{
    public function __invoke($item, ConditionalsContract $condition): bool;
}
