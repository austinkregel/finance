<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Institution::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'institution_id' => trim($faker->shuffleString($faker->sentence)),
        'logo' => $faker->imageUrl(),
        'site_url' => $faker->url,
        'products' => [],
        'primary_color' => $faker->hexColor,
    ];
});
