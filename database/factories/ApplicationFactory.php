<?php

namespace Database\Factories;

use App\Models\Gig;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gig_id' => Gig::factory(),
            'tutor_id' => User::factory(),
            'proposal_message' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }
}
