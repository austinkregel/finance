<?php

declare(strict_types=1);

namespace App\Actions;

abstract class Action
{
    abstract public function handle(): void;

    abstract public function validate(): array;
}
