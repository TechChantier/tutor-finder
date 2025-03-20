<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gig;
use App\Models\Category;
use App\Models\TutorProfile;
use App\Models\Qualification;
use App\Models\Application;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. First run CategorySeeder since other models depend on categories
        $this->call(
            CategorySeeder::class,
            // GigSeeder::class,
        );

        // 2. Create test users
        $testTutor = User::factory()->create([
            'name' => 'Test Tutor',
            'email' => 'tutor@example.com',
            'user_type' => 'tutor',
        ]);

        $testLearner = User::factory()->create([
            'name' => 'Test Learner',
            'email' => 'learner@example.com',
            'user_type' => 'learner',
        ]);

        // 3. Create regular users
        $tutors = User::factory(10)->state(['user_type' => 'tutor'])->create();
        $learners = User::factory(20)->state(['user_type' => 'learner'])->create();

        // 4. Create tutor profiles and assign categories
        User::where('user_type', 'tutor')->each(function ($tutor) {
            // Create profile
            TutorProfile::factory()->create([
                'user_id' => $tutor->id
            ]);
            
            // Assign 2-3 random categories to each tutor
            $categories = Category::inRandomOrder()->take(rand(2, 3))->pluck('id');
            $tutor->categories()->attach($categories);
            
            // Create 1-3 qualifications for each tutor
            Qualification::factory()
                ->count(rand(1, 3))
                ->create(['tutor_id' => $tutor->id]);
        });

        // 5. Create gigs for learners
        User::where('user_type', 'learner')->each(function ($learner) {
            Gig::factory()
                ->count(rand(1, 3))
                ->create([
                    'learner_id' => $learner->id,
                    'category_id' => Category::inRandomOrder()->first()->id
                ]);
        });

        // 6. Create applications for open gigs
        Gig::where('status', 'open')->each(function ($gig) {
            // Get 1-3 random tutors who haven't applied yet
            $tutors = User::where('user_type', 'tutor')
                ->whereDoesntHave('applications', function ($query) use ($gig) {
                    $query->where('gig_id', $gig->id);
                })
                ->inRandomOrder()
                ->take(rand(1, 3))
                ->get();

            foreach ($tutors as $tutor) {
                Application::factory()->create([
                    'gig_id' => $gig->id,
                    'tutor_id' => $tutor->id
                ]);
            }
        });
    }
}