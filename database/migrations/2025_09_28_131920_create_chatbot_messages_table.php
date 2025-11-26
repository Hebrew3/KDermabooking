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
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->onDelete('cascade');
            $table->enum('sender_type', ['client', 'bot']); // Who sent the message
            $table->text('message'); // The actual message content
            $table->json('message_data')->nullable(); // Additional data like recommendations, buttons, etc.
            $table->enum('message_type', ['text', 'recommendation', 'appointment_suggestion', 'quick_reply'])->default('text');
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable(); // Store additional metadata like intent, confidence, etc.
            $table->timestamps();

            // Indexes
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_type', 'message_type']);
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
    }
};
