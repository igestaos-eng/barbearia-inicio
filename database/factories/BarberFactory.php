<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barber>
 */
class BarberFactory extends Factory
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
            'specialization' => fake()->randomElement(['Classic Cuts', 'Modern Styles', 'Beard Specialist', 'Kids Cuts']),
            'bio' => fake()->paragraph(),
            'photo' => null,
            'experience_years' => fake()->numberBetween(1, 20),
            'rating' => fake()->randomFloat(2, 3.0, 5.0),
            'total_reviews' => fake()->numberBetween(0, 100),
            'is_available' => true,
        ];
    }
}
