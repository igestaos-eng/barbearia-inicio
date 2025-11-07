<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceTypes = ['haircut', 'beard', 'styling', 'coloring', 'treatment', 'package'];
        $serviceNames = [
            'haircut' => ['Classic Haircut', 'Modern Cut', 'Fade', 'Buzz Cut'],
            'beard' => ['Beard Trim', 'Beard Shaping', 'Beard Grooming'],
            'styling' => ['Hair Styling', 'Blow Dry', 'Special Occasion Styling'],
            'coloring' => ['Hair Color', 'Highlights', 'Root Touch-up'],
            'treatment' => ['Hair Treatment', 'Scalp Treatment', 'Deep Conditioning'],
            'package' => ['Premium Package', 'Deluxe Package', 'Complete Grooming'],
        ];

        $type = fake()->randomElement($serviceTypes);
        $name = fake()->randomElement($serviceNames[$type]);

        return [
            'name' => $name,
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 20, 150),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60, 90, 120]),
            'image' => null,
            'service_type' => $type,
            'is_active' => fake()->boolean(90),
            'popularity' => fake()->numberBetween(0, 1000),
        ];
    }
}
