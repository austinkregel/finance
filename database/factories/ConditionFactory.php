<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConditionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Condition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'value' => $this->faker->word,
            'comparator' => 'EQUAL',
            'parameter' => $this->faker->word,
        ];
    }
}
