<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Budget::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\User::factory(),
            'name' => $this->faker->name,
            'amount' => $this->faker->numberBetween(10, 30),
            'frequency' => 1,
            'interval' => 'MONTHLY',
            'started_at' => $this->faker->dateTime,
            'count' => null,
            'breached_at' => null,
        ];
    }
}
