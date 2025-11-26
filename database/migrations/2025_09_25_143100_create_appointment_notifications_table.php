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
        Schema::create('appointment_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['staff_unavailable', 'appointment_reminder', 'appointment_confirmed', 'appointment_cancelled', 'staff_reassigned']);
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data like alternative staff options
            $table->boolean('is_read')->default(false);
            $table->boolean('requires_action')->default(false); // Client needs to respond
            $table->timestamp('expires_at')->nullable(); // For time-sensitive notifications
            $table->timestamps();

            // Indexes
            $table->index(['client_id', 'is_read']);
            $table->index(['appointment_id', 'type']);
            $table->index('requires_action');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_notifications');
    }
};
