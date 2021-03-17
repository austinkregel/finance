<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'is_possible_subscription' => false,
            'is_subscription' => false,
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'account_id' => fn () => \App\Models\Account::factory()->create()->account_id,
            'date' => $this->faker->date('Y-m-d'),
            'pending' => mt_rand(0, 1),
            'category_id' => fn () => \App\Models\Category::factory()->create()->category_id,
            'transaction_id' => \Illuminate\Support\Str::random(32),
            'transaction_type' => 'debit',
        ];
    }
}
