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
        Schema::create('connection_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained('users');
            $table->foreignId('tutor_id')->constrained('users');
            $table->text('message');
            $table->string('learner_whatsapp');
            $table->string('tutor_whatsapp')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->enum('period_type', ['weekly', 'monthly']);
            $table->decimal('amount_paid', 10, 2);
            $table->boolean('payment_completed')->default(false);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connection_requests');
    }
};
