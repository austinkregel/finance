<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Account::class, function (Faker $faker) {
    return [
        'account_id' => $faker->sentence,
        'mask' =>  $faker->bankAccountNumber,
        'name' => $faker->name,
        'official_name' => $faker->word,
        'balance' => $faker->randomFloat(2, 3),
        'available' => $faker->boolean,
        'subtype' =>  $faker->word,
        'type' =>  $faker->word,
        'access_token_id' => factory(\App\Models\AccessToken::class),
    ];
});
