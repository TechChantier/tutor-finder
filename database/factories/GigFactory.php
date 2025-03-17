<?php

namespace Database\Factories;

use App\Models\Gig;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class GigFactory extends Factory
{
    protected $model = Gig::class;

    public function definition(): array
    {
        $titles = [
            'Need help with %s',
            'Looking for a %s tutor',
            'Seeking experienced %s teacher',
            'Want to learn %s',
            'Require assistance with %s',
        ];

        $subjects = [
            'Mathematics', 'Physics', 'Chemistry', 'Biology',
            'French', 'English', 'Computer Programming',
            'Piano', 'Guitar', 'Vocal Training',
            'Web Development', 'Digital Marketing',
            'Business Studies', 'Accounting'
        ];

        $randomTitle = sprintf(
            fake()->randomElement($titles), 
            fake()->randomElement($subjects)
        );

        return [
            'learner_id' => User::factory()->learner(),
            'category_id' => Category::inRandomOrder()->first()?->id 
                ?? Category::factory(),
            'title' => $randomTitle,
            'description' => fake()->realTextBetween(200, 300),
            'budget' => fake()->randomElement([
                5000, 10000, 15000, 20000, 25000, 
                30000, 35000, 40000, 45000, 50000
            ]),
            'budget_period' => fake()->randomElement([
                'hourly', 'daily', 'weekly', 'monthly'
            ]),
            'location' => fake()->randomElement([
                'Douala', 'Yaoundé', 'Bamenda', 'Bafoussam',
                'Garoua', 'Maroua', 'Buea', 'Limbe',
                'Kribi', 'Kumba', 'Online'
            ]),
            'status' => 'pending', // Default to pending
        ];
    }

    /**
     * Indicate that the gig is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    /**
     * Indicate that the gig is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the gig is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the gig is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}