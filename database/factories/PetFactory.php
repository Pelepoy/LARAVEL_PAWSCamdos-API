<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::inRandomOrder()->first()?->id ?? User::factory(), // Associate with a random user or create one
            'species' => fake()->randomElement(['Dog', 'Cat', 'Rabbit', 'Bird', 'Fish']),
            'name' => fake()->firstName(),
            'breed' => fake()->word(),
            'color' => fake()->safeColorName(),
            'age' => fake()->numberBetween(1, 20),
            'weight' => fake()->randomFloat(2, 1, 50), // Random weight between 1kg and 50kg
            'description' => fake()->sentence(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->date(),
            'microchip_id' => fake()->optional()->uuid(),
            'insurance_policy_number' => fake()->optional()->bothify('POL-####-####'),
            'is_vaccinated' => fake()->boolean(),
            'is_neutered' => fake()->boolean(),
            'file_name' => fake()->optional()->word() . '.jpg',
            'profile_image_url' => fake()->imageUrl(200, 200, 'animals', true, 'pets'), // Random pet image
        ];
    }
}