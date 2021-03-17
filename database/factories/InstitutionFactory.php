<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InstitutionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Institution::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'institution_id' => trim($this->faker->shuffleString($this->faker->sentence)),
            'logo' => $this->faker->imageUrl(),
            'site_url' => $this->faker->url,
            'products' => [],
            'primary_color' => $this->faker->hexColor,
        ];
    }
}
