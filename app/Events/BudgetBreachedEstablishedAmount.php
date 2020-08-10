<?php

namespace App\Events;

use App\Budget;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetBreachedEstablishedAmount
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $budget;

    public function __construct(Budget $budget)
    {
        $this->budget = $budget;
    }

    public function getBudget(): Budget
    {
        return $this->budget;
    }
}
