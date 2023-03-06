<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->unique()->randomNumber(),
            'color' => $this->faker->word(),
            'maker' => $this->faker->word(),
            'model' => $this->faker->word(),
            'year' => $this->faker->year(),
            'vin' => $this->faker->unique()->randomNumber(),
        ];
    }
}
