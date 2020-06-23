<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;


$factory->define(App\Models\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'category_id' => $faker->randomNumber(),
    ];
});
