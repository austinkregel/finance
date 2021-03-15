<?php

namespace App\Console;

use App\Jobs\CheckBudgetsForBreachesOfAmount;
use App\Jobs\UpdateWebhooksForAccountsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('plaid:sync-institutions')->monthly();
        $schedule->command('plaid:sync-categories')->monthly();
        $schedule->command('sync:plaid 1')->hourlyAt(0);
        // Small job offset so we don't flood the queue. Not really ever going to be a problem... but meh :shrug:
        $schedule->job(CheckBudgetsForBreachesOfAmount::class)->hourlyAt(10);
        $schedule->job(UpdateWebhooksForAccountsJob::class)->daily();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
