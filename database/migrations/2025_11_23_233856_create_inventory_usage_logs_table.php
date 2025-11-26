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
        Schema::create('inventory_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->string('item_name'); // Store name at time of usage
            $table->string('item_sku'); // Store SKU at time of usage
            $table->string('usage_type')->default('service'); // service, pos, manual, etc.
            $table->integer('quantity_deducted')->default(0)->comment('Quantity deducted (for non-mL items)');
            $table->decimal('volume_ml_deducted', 10, 2)->default(0)->comment('Volume in mL deducted (for mL-tracked items)');
            $table->decimal('stock_before', 10, 2)->nullable()->comment('Stock/volume before deduction');
            $table->decimal('stock_after', 10, 2)->nullable()->comment('Stock/volume after deduction');
            $table->string('unit')->nullable()->comment('Unit of measurement (piece, bottle, mL, etc.)');
            $table->boolean('is_ml_tracking')->default(false)->comment('Whether this item uses mL tracking');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('appointment_id');
            $table->index('inventory_item_id');
            $table->index('service_id');
            $table->index('usage_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_usage_logs');
    }
};
