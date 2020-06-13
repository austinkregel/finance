<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'is_possible_subscription' => false,
        'is_subscription' => false,
        'amount' => $faker->randomFloat(2, 1, 1000),
        'account_id' => function() {
            return factory(\App\Models\Account::class)->create()->account_id;
        },
        'date' => $faker->date('Y-m-d'),
        'pending' => mt_rand(0, 1),
        'category_id' => \App\Models\Category::all()->random()->first()->category_id,
        'transaction_id' => \Illuminate\Support\Str::random(32),
        'transaction_type' => 'debit',
    ];
});
