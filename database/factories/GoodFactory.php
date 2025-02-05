<?php

namespace Database\Factories;

use App\Models\Good;
use App\Models\GoodType;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_ru' => fake()->colorName(),
            'name_en' => fake()->colorName(),
            'description_ru' => fake()->unique()->safeEmail(),
            'description_en' => fake()->unique()->safeEmail(),
            'cost' => fake()->numberBetween(10000, 50000),
            'discount_cost' => rand(0, 1) ? fake()->numberBetween(10000, 50000) : null,
            'additional_cost' => 0,
            'related_goods' => json_encode(Good::query()->inRandomOrder()->pluck('id')),
            'additionals' => json_encode(Good::query()->inRandomOrder()->pluck('id')),
            'good_type_id' => GoodType::query()->inRandomOrder()->first(),
        ];
    }
}
