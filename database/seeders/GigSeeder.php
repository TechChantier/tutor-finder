<?php

namespace Database\Seeders;

use App\Models\Gig;
use Illuminate\Database\Seeder;

class GigSeeder extends Seeder
{
    public function run(): void
    {
        // Create 16 open gigs
        Gig::factory()->count(16)->open()->create();
    }
}