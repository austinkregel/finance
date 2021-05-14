<?php
declare(strict_types=1);

namespace App\Events;

use App\Contracts\Events\TransactionEventContract;
use App\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionUpdated implements TransactionEventContract
{
    use Dispatchable, SerializesModels;

    public $transaction;

    public $shouldSendAlerts = true;

    public function __construct(Transaction $transaction, ?bool $shouldSendAlerts = true)
    {
        $this->transaction = $transaction;
        $this->shouldSendAlerts = $shouldSendAlerts;
    }

    public function getData(): array
    {
        return [
            'transaction' => $this->transaction,
        ];
    }

    public function getShouldSendAlerts(): bool
    {
        return $this->shouldSendAlerts;
    }
}
