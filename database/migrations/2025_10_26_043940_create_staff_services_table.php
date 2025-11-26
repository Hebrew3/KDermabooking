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
        Schema::create('staff_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->boolean('is_primary')->default(false); // Whether this is the staff's primary service
            $table->decimal('custom_price', 10, 2)->nullable(); // Custom price for this staff-service combo
            $table->integer('proficiency_level')->default(1); // 1-5 proficiency level
            $table->text('notes')->nullable(); // Any special notes
            $table->timestamps();

            // Ensure unique staff-service combinations
            $table->unique(['staff_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_services');
    }
};
