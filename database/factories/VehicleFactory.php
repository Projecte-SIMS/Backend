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
        $brands = ['Toyota', 'Honda', 'Tesla', 'BMW', 'Mercedes', 'Volkswagen', 'Renault', 'Peugeot'];
        $models = ['Model S', 'Corolla', 'Civic', 'Golf', 'Clio', '308', 'Model 3', 'i3'];

        return [
            'license_plate' => strtoupper(fake()->unique()->bothify('####???')),
            'brand' => fake()->randomElement($brands),
            'model' => fake()->randomElement($models),
            'active' => false, // Available by default (active=false means not in use)
            'price_per_minute' => fake()->randomFloat(2, 0.10, 0.50),
            'image_url' => null,
        ];
    }

    /**
     * Indicate that the vehicle is currently in use.
     */
    public function inUse(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }
}
