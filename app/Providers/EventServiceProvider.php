<?php

namespace App\Providers;

use App\Events\RegroupEvent;
use App\Events\TransactionCreated;
use App\Events\TransactionUpdated;
use App\Listeners\ApplyGroupToTransactionAutomaticallyListener;
use App\Listeners\ApplyTransactionCategoriesListener;
use App\Listeners\CreateDefaultTagsForUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            CreateDefaultTagsForUser::class,
        ],

        TransactionUpdated::class => [
            ApplyTransactionCategoriesListener::class,
            ApplyGroupToTransactionAutomaticallyListener::class,
        ],
        TransactionCreated::class => [
            ApplyTransactionCategoriesListener::class,
            ApplyGroupToTransactionAutomaticallyListener::class,
        ],

        RegroupEvent::class => [
            ApplyGroupToTransactionAutomaticallyListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
