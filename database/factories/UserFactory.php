<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->define(App\Models\Transaction::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'is_possible_subscription' => false,
        'is_subscription' => false,
        'amount' => $faker->randomFloat(2, 1, 1000),
        'account_id' => fn () => factory(\App\Models\Account::class)->create()->account_id,
        'date' => $faker->date('Y-m-d'),
        'pending' => mt_rand(0, 1),
        'category_id' =>  fn () => factory(\App\Models\Category::class)->create()->category_id,
        'transaction_id' => \Illuminate\Support\Str::random(32),
        'transaction_type' => 'debit',
    ];
});
