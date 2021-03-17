<?php

namespace App\Actions;

abstract class Action
{
    abstract public function handle(): void;

    abstract public function validate(): array;
}
