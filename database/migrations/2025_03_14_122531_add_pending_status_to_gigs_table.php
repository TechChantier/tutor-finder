<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Gig;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gigs', function (Blueprint $table) {
            $table->enum('status', ['pending', 'open', 'in_progress', 'completed', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // First update any rows with 'pending' status to 'open'
        DB::table('gigs')->where('status', 'pending')->update(['status' => 'open']);
        
        Schema::table('gigs', function (Blueprint $table) {
            // Now it's safe to modify the enum
            DB::statement("ALTER TABLE gigs MODIFY status ENUM('open', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'open'");
        });
    }
};