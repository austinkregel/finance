<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $name = ['en' => $this->faker->sentence],
            'slug' => $name,
            'type' => 'automatic',
            'order_column' => 1,
            'user_id' => \App\User::factory(),
        ];
    }
}
