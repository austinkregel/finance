<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\AccessToken::class, function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class),
        'token' => trim($faker->shuffleString($faker->sentence)),
        'error' => '',
        'should_sync' => true,
        'institution_id' => factory(\App\Models\Institution::class),
    ];
});
