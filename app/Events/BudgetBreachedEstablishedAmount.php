<?php

declare(strict_types=1);

namespace App\Events;

use App\Budget;
use App\Contracts\Events\TransactionEventContract;
use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetBreachedEstablishedAmount implements TransactionEventContract
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Budget $budget;

    public ?Transaction $transaction;

    public function __construct(Budget $budget, ?Transaction $transaction = null)
    {
        $this->budget = $budget;
        $this->transaction = $transaction;
    }

    public function getData(): array
    {
        return [
            'transaction' => $this->transaction,
            'budget' => $this->budget,
        ];
    }

    public function getShouldSendAlerts(): bool
    {
        return true;
    }
}
