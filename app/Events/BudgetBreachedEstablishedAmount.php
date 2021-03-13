<?php

namespace App\Events;

use App\Budget;
use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetBreachedEstablishedAmount
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Budget $budget;

    public ?Transaction $transaction;

    public function __construct(Budget $budget, ?Transaction $transaction = null)
    {
        $this->budget = $budget;
        $this->transaction = $transaction;
    }

    public function getBudget(): Budget
    {
        return $this->budget;
    }

    public function getTransaction():? Transaction
    {
        return $this->transaction;
    }
}
