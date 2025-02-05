<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GoodTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->colorName(),
            'code' => fake()->colorName(),
            'icon' => fake()->colorName(),
            'description' => fake()->unique()->safeEmail(),
        ];
    }
}
