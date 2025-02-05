<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::query()->inRandomOrder()->first(),
            'status' => ['in_rent', 'returned'][$this->faker->numberBetween(0, 1)],
            'amount_paid' => $this->faker->randomNumber(),
        ];
    }
}
