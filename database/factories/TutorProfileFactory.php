<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TutorProfile>
 */
class TutorProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'user_id' => User::factory(),
            'bio' => fake()->paragraphs(2, true),
            'years_of_experience' => fake()->numberBetween(1, 20),
            'verification_status' => fake()->randomElement(['pending', 'verified', 'rejected']),
            'availability_status' => fake()->randomElement(['available', 'busy']),
        ];
    }
}
