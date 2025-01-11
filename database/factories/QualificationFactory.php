<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Qualification>
 */
class QualificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tutor_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'institution' => fake()->company(),
            'year_obtained' => fake()->year(),
            'verification_status' => fake()->randomElement(['pending', 'verified', 'rejected']),
        ];
    }
}
