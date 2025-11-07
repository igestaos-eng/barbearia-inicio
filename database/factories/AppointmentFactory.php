<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scheduledAt = fake()->dateTimeBetween('now', '+30 days');
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];

        return [
            'customer_id' => \App\Models\Customer::factory(),
            'barber_id' => \App\Models\Barber::factory(),
            'service_id' => \App\Models\Service::factory(),
            'scheduled_at' => $scheduledAt,
            'completed_at' => null,
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'status' => fake()->randomElement($statuses),
            'notes' => fake()->optional()->sentence(),
            'cancellation_reason' => null,
            'price' => fake()->randomFloat(2, 20, 150),
            'reminder_sent' => false,
            'reminder_sent_at' => null,
        ];
    }
}
