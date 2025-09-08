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
            'start_location' => fake()->randomElement(['home', 'work']),
            'scheduled_start_time' => fake()->dateTimeBetween('-30 days', 'now'),
            'company_id' => \App\Models\Company::factory(),
            'driver_id' => \App\Models\Driver::factory(),
            'vehicle_id' => \App\Models\Vehicle::factory(),
        ];
    }
}
