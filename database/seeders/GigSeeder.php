<?php

namespace Database\Seeders;

use App\Models\Gig;
use Illuminate\Database\Seeder;

class GigSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 gigs with random statuses
        Gig::factory()->count(5)->create();

        // Create 10 open gigs
        Gig::factory()->count(16)->open()->create();

        // Create 5 completed gigs
        Gig::factory()->count(3)->completed()->create();

        // Create 5 cancelled gigs
        Gig::factory()->count(3)->cancelled()->create();

        // Create 5 pending gigs
        Gig::factory()->count(4)->pending()->create();
    }
}