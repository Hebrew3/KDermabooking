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
        Schema::table('appointments', function (Blueprint $table) {
            // Make client_id nullable for walk-in customers
            $table->foreignId('client_id')->nullable()->change();
            
            // Add walk-in customer information fields
            $table->string('walkin_customer_name')->nullable()->after('client_id');
            $table->string('walkin_customer_email')->nullable()->after('walkin_customer_name');
            $table->string('walkin_customer_phone')->nullable()->after('walkin_customer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Remove walk-in customer fields
            $table->dropColumn(['walkin_customer_name', 'walkin_customer_email', 'walkin_customer_phone']);
            
            // Note: Making client_id required again might fail if there are appointments with null client_id
            // You may need to handle this manually
        });
    }
};
