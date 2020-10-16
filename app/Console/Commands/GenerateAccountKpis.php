<?php

namespace App\Console\Commands;

use App\AccountKpi;
use App\Models\Account;
use Illuminate\Console\Command;

class GenerateAccountKpis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:account-kpis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build account KPIs for the accounts.';

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
        $nowTime = now();

        Account::each(function (Account $account) use ($nowTime) {
            $kpi = AccountKpi::where([
                'account_id' => $account->account_id,
                'date' => $nowTime->format('Y-m-d')
            ])->first();

            if ($kpi) {
                return $kpi->update([
                    'balance' => $account->balance,
                    'available' => $account->available,
                    'total_transactions_today' => $account->transactions()
                        ->where('date', $nowTime->format('Y-m-d'))
                        ->count(),
                    'total_subscriptions_today' => $account->transactions()
                        ->whereHas('subscription', function ($query): void {
                            $query->where('type', 'subscription');
                        })
                        ->where('date', $nowTime->format('Y-m-d'))
                        ->sum('amount'),
                    'total_bills_today' => $account->transactions()
                        ->whereHas('subscription', function ($query): void {
                            $query->where('type', 'bill');
                        })
                        ->where('date', $nowTime->format('Y-m-d'))
                        ->sum('amount'),
                    'total_spends_today' => $account->transactions()
                        ->whereHas('subscription', function ($query): void {
                            $query->where('type', 'subscription');
                        })
                        ->where('date', $nowTime->format('Y-m-d'))
                        ->sum('amount'),
                ]);
            }

            $account->kpis()->create([
                'date' => $nowTime,
                'account_id' => $account->account_id,
                'balance' => $account->balance,
                'available' => $account->available,
                'total_transactions_today' => $account->transactions()
                    ->where('date', $nowTime->format('Y-m-d'))
                    ->count(),
                'total_subscriptions_today' => $account->transactions()
                    ->whereHas('subscription', function ($query): void {
                        $query->where('type', 'subscription');
                    })
                    ->where('date', $nowTime->format('Y-m-d'))
                    ->sum('amount'),
                'total_bills_today' => $account->transactions()
                    ->whereHas('subscription', function ($query): void {
                        $query->where('type', 'bill');
                    })
                    ->where('date', $nowTime->format('Y-m-d'))
                    ->sum('amount'),
                'total_spends_today' => $account->transactions()
                    ->whereHas('subscription', function ($query): void {
                        $query->where('type', 'subscription');
                    })
                    ->where('date', $nowTime->format('Y-m-d'))
                    ->sum('amount'),
            ]);
        });
    }
}
