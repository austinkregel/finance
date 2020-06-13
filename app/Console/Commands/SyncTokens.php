<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the accounts from the stored plaid access tokens.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
