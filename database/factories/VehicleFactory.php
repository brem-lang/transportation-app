<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'make' => fake()->name(5),
            'model' => fake()->year(),
            'license_plate' => fake()->unique()->bothify('???-####'),
            'company_id' => \App\Models\Company::factory(),
        ];
    }
}
