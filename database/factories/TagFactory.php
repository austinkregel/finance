<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Tag::class, function (Faker $faker) {
    return [
        'name' => $name = ['en' => $faker->sentence],
        'slug' => $name,
        'type' => 'automatic',
        'order_column' => 1,
        'user_id' => factory(\App\User::class),
    ];
});
