<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Remove whatsapp_number column from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });

        // 2. Add profile_video field to tutor_profiles table if it exists
        if (Schema::hasTable('tutor_profiles')) {
            Schema::table('tutor_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('tutor_profiles', 'profile_video')) {
                    $table->string('profile_video')->nullable()->after('availability_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add whatsapp_number column back to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('phone_number');
        });

        // 2. Remove profile_video field from tutor_profiles table if it exists
        if (Schema::hasTable('tutor_profiles')) {
            Schema::table('tutor_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('tutor_profiles', 'profile_video')) {
                    $table->dropColumn('profile_video');
                }
            });
        }
    }
};