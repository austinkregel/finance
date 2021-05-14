<?php
declare(strict_types=1);

namespace App\Events;

use App\Contracts\Events\TransactionEventContract;
use App\Models\Transaction;
use App\Tag;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionGroupedEvent implements TransactionEventContract
{
    use Dispatchable, SerializesModels;

    public $tag;

    public $transaction;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Tag $tag, Transaction $transaction)
    {
        $this->tag = $tag;
        $this->transaction = $transaction;
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function getShouldSendAlerts(): bool
    {
        return true;
    }
}
