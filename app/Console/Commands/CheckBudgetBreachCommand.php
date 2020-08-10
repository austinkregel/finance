<?php

namespace App\Console\Commands;

use App\Jobs\CheckBudgetsForBreachesOfAmount;
use Illuminate\Console\Command;

class CheckBudgetBreachCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:budget-breach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the budgets to see if they\'re over budget';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        CheckBudgetsForBreachesOfAmount::dispatch();
    }
}
