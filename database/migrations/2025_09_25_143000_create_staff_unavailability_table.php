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
        Schema::create('staff_unavailability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->date('unavailable_date');
            $table->time('start_time')->nullable(); // null means entire day
            $table->time('end_time')->nullable();
            $table->enum('reason', ['emergency', 'sick_leave', 'personal_leave', 'vacation', 'training', 'other'])->default('other');
            $table->text('notes')->nullable();
            $table->boolean('is_emergency')->default(false);
            $table->timestamp('reported_at')->useCurrent();
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['staff_id', 'unavailable_date']);
            $table->index(['unavailable_date', 'is_emergency']);
            $table->index('is_emergency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_unavailability');
    }
};
