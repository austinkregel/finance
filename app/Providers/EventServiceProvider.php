<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\BudgetBreachedEstablishedAmount;
use App\Events\RegroupEvent;
use App\Events\TransactionCreated;
use App\Events\TransactionGroupedEvent;
use App\Events\TransactionUpdated;
use App\Listeners\ApplyGroupToTransactionAutomaticallyListener;
use App\Listeners\CreateDefaultAlertsForUser;
use App\Listeners\CreateDefaultBudgetsForUser;
use App\Listeners\CreateDefaultTagsForUser;
use App\Listeners\TriggerAlertForBreachedBudget;
use App\Listeners\TriggerAlertIfConditionsPassListenerForTransaction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            CreateDefaultTagsForUser::class,
            CreateDefaultAlertsForUser::class,
            CreateDefaultBudgetsForUser::class,
        ],

        TransactionUpdated::class => [
            ApplyGroupToTransactionAutomaticallyListener::class,
            TriggerAlertIfConditionsPassListenerForTransaction::class,
        ],

        TransactionCreated::class => [
            ApplyGroupToTransactionAutomaticallyListener::class,
            TriggerAlertIfConditionsPassListenerForTransaction::class,
        ],

        RegroupEvent::class => [
            ApplyGroupToTransactionAutomaticallyListener::class,
        ],

        BudgetBreachedEstablishedAmount::class => [
            TriggerAlertForBreachedBudget::class,
        ],

        TransactionGroupedEvent::class => [
            // A transaction was newly grouped into some group. Do something.
            TriggerAlertIfConditionsPassListenerForTransaction::class,
        ],
    ];
}
