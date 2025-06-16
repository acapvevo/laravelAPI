<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'line_1' => fake()->streetAddress(),
            'line_2' => fake()->secondaryAddress(),
            'line_3' => fake()->optional()->streetAddress(),
            'postcode' => fake()->postcode(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->country(),
        ];
    }
}
