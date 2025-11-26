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
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->enum('status', ['active', 'ended', 'archived'])->default('active');
            $table->json('client_preferences')->nullable(); // Store client preferences for recommendations
            $table->json('conversation_context')->nullable(); // Store conversation context for better responses
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index(['client_id', 'status']);
            $table->index('session_id');
            $table->index('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversations');
    }
};
