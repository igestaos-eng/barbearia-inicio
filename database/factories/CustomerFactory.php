<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'birth_date' => fake()->optional()->date(),
            'notes' => fake()->optional()->sentence(),
            'preferences' => fake()->optional()->sentence(),
            'total_appointments' => fake()->numberBetween(0, 50),
            'last_visit_at' => fake()->optional()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
