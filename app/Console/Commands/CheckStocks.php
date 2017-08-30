<?php

namespace App\Console\Commands;

use App\Alerts;
use App\Notifications\PriceAboveTarget;
use App\Stock;
use App\UserStocks;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CheckStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the stocks price';

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
        $now = Carbon::now();
        $marketStart = Carbon::create($now->year, $now->month, $now->day, 9, 30, 0);
        $marketEnd = Carbon::create($now->year, $now->month, $now->day, 16, 0, 0);

//        if($now->greaterThan($marketEnd) || $now->lessThan($marketStart)) {
//            $this->info('The market is not active right now');
//            return;
//        }

        /** @var Collection $stocks */
        $stocks = UserStocks::with('stock')->where('is_watched', true)->get()->map->stock->map->ticker;

        $client = new Client([
            'url' => ''
        ]);
        $client->post('http://localhost:4000/' .  $stocks->implode(','));
        $data = $client->get('http://localhost:4000/' .  $stocks->implode(','))->getBody()->getContents();

        $decoded = collect(json_decode($data)->quotes);

        $freshStocks = Stock::whereIn('ticker', $decoded->map->ticker)->get();

        $freshStocks = $freshStocks->each(function ($dbStock) use ($decoded) {
            $decoded->each(function($responseStock) use ($dbStock) {
                if($responseStock->ticker !== $dbStock->ticker) {
                    return;
                }
                $dbStock->last_price = $responseStock->price;
                $dbStock->change = $responseStock->change;
                $dbStock->chance_percent = $responseStock->change_percent;
                $dbStock->dividend = $responseStock->dividend;
                $dbStock->yield = $responseStock->yield;
                $dbStock->save();
            });
            return $dbStock;
        });

        $this->info('Stocks updated');

        $this->info('Checking alerts');

        $alerts = Alerts::with('user', 'stock')->get();

        $alerts->each(function ($alert) {
            if((float)$alert->stock->last_price > (float)$alert->target_price) {
                $alert->user->notify(new PriceAboveTarget($alert));
                $this->info('We have reached a threashhold');
            } else {
                $this->info('No news is good news');
            }
        });

    }

}
