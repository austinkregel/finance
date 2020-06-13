<?php

namespace App\Events;

use App\Contracts\Events\TransactionEventContract;
use App\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated implements TransactionEventContract
{
    use Dispatchable, SerializesModels;

    public $transaction;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function getTransaction() :Transaction
    {
        return $this->transaction;
    }
}
