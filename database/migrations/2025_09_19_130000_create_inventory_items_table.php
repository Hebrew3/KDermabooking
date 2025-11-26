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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('brand')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->integer('maximum_stock')->nullable();
            $table->string('unit')->default('piece'); // piece, bottle, ml, etc.
            $table->string('supplier')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('storage_location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('images')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['category', 'is_active']);
            $table->index(['current_stock', 'minimum_stock']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
