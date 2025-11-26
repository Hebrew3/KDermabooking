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
        Schema::table('inventory_usage_logs', function (Blueprint $table) {
            $table->decimal('quantity_deducted', 10, 3)->default(0)->change()->comment('Quantity deducted (for non-mL items, supports decimals for partial quantities)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_usage_logs', function (Blueprint $table) {
            $table->integer('quantity_deducted')->default(0)->change()->comment('Quantity deducted (for non-mL items)');
        });
    }
};

