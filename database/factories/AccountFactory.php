<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => $this->faker->sentence,
            'mask' => $this->faker->bankAccountNumber,
            'name' => $this->faker->name,
            'official_name' => $this->faker->word,
            'balance' => $this->faker->randomFloat(2, 3),
            'available' => $this->faker->boolean,
            'subtype' => $this->faker->word,
            'type' => $this->faker->word,
            'access_token_id' => \App\Models\AccessToken::factory(),
        ];
    }
}
