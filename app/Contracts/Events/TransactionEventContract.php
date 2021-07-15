<?php
declare(strict_types=1);

namespace App\Contracts\Events;

interface TransactionEventContract
{
    public function getData(): array;

    public function getShouldSendAlerts(): bool;
}
