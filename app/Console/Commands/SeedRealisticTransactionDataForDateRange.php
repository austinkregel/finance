<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedRealisticTransactionDataForDateRange extends Command
{
    protected $signature = 'seed:realistically {accountId} {startDate} {endDate}';

    protected $description = 'Seeds realistic-ish data.';

    public const TRANSACTION_NAME = [
        "Uber 072515 SF**POOL**",
        "Tectra Inc",
        "AUTOMATIC PAYMENT - THANK",
        "KFC",
        "Madison Bicycle Shop",
        "CREDIT CARD 3333 PAYMENT *//",
        "Uber 063015 SF**POOL**",
        "ACH Electronic CreditGUSTO PAY 123456",
        "CD DEPOSIT .INITIAL.",
        "Touchstone Climbing",
        "United Airlines",
        "McDonald's",
        "Starbucks",
        "SparkFun",
        "INTRST PYMNT",
        "United Airlines",
        "Uber 072515 SF**POOL**",
        "Tectra Inc",
        "AUTOMATIC PAYMENT - THANK",
        "KFC",
        "Madison Bicycle Shop",
        "CREDIT CARD 3333 PAYMENT *//",
        "Uber 063015 SF**POOL**",
        "ACH Electronic CreditGUSTO PAY 123456",
        "CD DEPOSIT .INITIAL.",
        "Touchstone Climbing",
        "United Airlines",
        "McDonald's",
        "Starbucks",
        "SparkFun",
        "INTRST PYMNT",
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startDate = Carbon::parse($this->argument('startDate'));
        $endDate = Carbon::parse($this->argument('endDate'));

        $faker = Factory::create();

        $diff = $startDate->diffInDays($endDate);

        $accountId = $this->argument('accountId');

        for ($i = 0; $i < $diff; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $transactionsToday = random_int(0, 7);
            for ($j = 0; $j < $transactionsToday; $j++) {
                $pending = now()->diffInDays($currentDate) < 4;

                $transactionId = Str::random(37);

                $categories = Category::inRandomOrder()->limit(random_int(1, 3))->get();

                $categoryNames = $categories->map->name->join('", "');

                $name = static::TRANSACTION_NAME[random_int(0, count(static::TRANSACTION_NAME) - 1)];

                Transaction::create([
                    'name' => $name,
                    'is_possible_subscription' => false,
                    'is_subscription' => false,
                    'amount' => $amount = (random_int(100, 9999) / 100),
                    'account_id' => $accountId,
                    'date' => $currentDate->format('Y-m-d'),
                    'pending' => $pending,
                    'category_id' => Category::inRandomOrder()->first()->category_id,
                    'transaction_id' => $pending ? null : $transactionId,
                    'transaction_type' => 'place',
                    'pending_transaction_id' => $pending ? $transactionId : null,
                    'data' => [
                        "date" => $currentDate->format('Y-m-d'),
                        "name" => $name,
                        "amount" => $amount,
                        "pending" => $pending,
                        "category" => $categoryNames,
                        "location" => [
                            "lat" => null,
                            "lon" => null,
                            "zip" => null,
                            "city" => null,
                            "state" => null,
                            "address" => null,
                            "store_number" => null
                        ],
                        "account_id" => $accountId,
                        "category_id" => $categories->first()->category_id,
                        "payment_meta" => [
                            "payee" => null,
                            "payer" => null,
                            "ppd_id" => null,
                            "reason" => null,
                            "by_order_of" => null,
                            "payment_method" => null,
                            "reference_number" => null,
                            "payment_processor" => null
                        ],
                        "account_owner" => null,
                        "merchant_name" => $faker->company,
                        "transaction_id" => $pending ? null : $transactionId,
                        "authorized_date" => null,
                        "payment_channel" => "in store",
                        "transaction_type" => "place",
                        "pending_transaction_id" => $pending ? $transactionId : null
                    ]
                ]);
            }
        }
    }
}
