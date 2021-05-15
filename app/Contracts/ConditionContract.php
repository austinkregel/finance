<?php
declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface ConditionContract
{
    public function __invoke(Arrayable $item, ConditionalsContract $condition): bool;
}
