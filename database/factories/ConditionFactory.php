<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;


$factory->define(App\Condition::class, function (Faker $faker) {
    return [
        'value' => $faker->word,
        'comparator' => 'EQUAL',
        'parameter' => $faker->word,
    ];
});
