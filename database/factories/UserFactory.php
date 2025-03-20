<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
      public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        
        return [
            'name' => fake()->name($gender),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone_number' => fake()->phoneNumber(),
            // 'whatsapp_number' => fake()->phoneNumber(),
            'user_type' => fake()->randomElement(['tutor', 'learner']),
            'location' => fake()->city(),
            'profile_image' => 'avatars/default-' . $gender . '.png',
        ];
    }

       /**
     * Indicate that the user is a tutor.
     */
    public function tutor(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'tutor',
        ]);
    }

    /**
     * Indicate that the user is a learner.
     */
    public function learner(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'learner',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
