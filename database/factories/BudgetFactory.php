<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Budget::class, function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class),
        'name' => $faker->name,
        'amount' => $faker->numberBetween(10, 30),
        'frequency' => 1,
        'interval' => 'MONTHLY',
        'started_at' => $faker->dateTime,
        'count' => null,
        'breached_at' => null,
    ];
});
