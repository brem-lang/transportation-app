<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed']),
            'start_location' => [
                'lat' => fake()->randomFloat(10, 100),
                'lng' => fake()->randomFloat(10, 100),
            ],
            'scheduled_start_time' => fake()->dateTimeBetween('-30 days', 'now'),
            'scheduled_end_time' => now()->addDays(2),
            'company_id' => \App\Models\Company::factory(),
            'driver_id' => \App\Models\Driver::factory(),
            'vehicle_id' => \App\Models\Vehicle::factory(),
        ];
    }
}
