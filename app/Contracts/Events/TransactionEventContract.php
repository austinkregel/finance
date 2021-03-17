<?php

namespace App\Contracts\Events;

use App\Models\Transaction;

interface TransactionEventContract
{
    public function getTransaction(): Transaction;

    public function getShouldSendAlerts(): bool;
}
