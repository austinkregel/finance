<?php

namespace App\Listeners;

use App\Contracts\Events\TransactionEventContract;
use App\Events\TransactionCreated;
use App\Events\TransactionUpdated;
use App\Models\Category;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplyTransactionCategoriesListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TransactionCreated|TransactionUpdated $event
     * @return void
     */
    public function handle(TransactionEventContract $event)
    {
        $localTransaction = $event->getTransaction();

        $categories = $transaction->category ?? [];

        $localTransaction->categories()->sync([]);

        foreach ($categories as $category) {
            $localTransaction->categories()->sync([
                cache()->rememberForever('category.'.$category, function () use ($category) {
                    return Category::where('name', $category)->first();
                })->id
            ], false);
        }
    }
}
