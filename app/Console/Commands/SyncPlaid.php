<?php

namespace App\Console\Commands;

use App\Jobs\SyncPlaidAccountsJob;
use App\Jobs\SyncPlaidTransactionsJob;
use App\Models\AccessToken;
use App\Models\Account;
use Illuminate\Console\Command;

class SyncPlaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:plaid {range}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync account balances and plaid transaction for range in months.';

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
        $this->info('Looking for more transactions');
        foreach (AccessToken::all() as $token) {
            SyncPlaidAccountsJob::dispatchNow($token);
            SyncPlaidTransactionsJob::dispatchNow($token, now()->subDays((int) $this->argument('range')), now());
        }
    }
}
