<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    public function updating(Transaction $transaction) {
        if ($transaction->isDirty('is_subscription') && $transaction->is_subscription) {
            $transaction->associateBillName();
            $transaction->createSubscription();
        }
    }
    public function updated(Transaction $transaction) {
        Transaction::where('name', $transaction->name)->update([
            'is_possible_subscription' => $transaction->is_possible_subscription,
            'is_subscription' => $transaction->is_subscription,
        ]);
    }
}
