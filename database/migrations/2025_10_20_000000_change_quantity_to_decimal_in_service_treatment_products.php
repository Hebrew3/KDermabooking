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
        Schema::table('service_treatment_products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->default(1)->change()->comment('Quantity of product used per service (supports decimals for partial quantities)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_treatment_products', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->change()->comment('Quantity of product used per service');
        });
    }
};

