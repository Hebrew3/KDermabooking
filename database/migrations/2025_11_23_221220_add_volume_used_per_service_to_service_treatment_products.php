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
            $table->decimal('volume_used_per_service', 10, 2)->nullable()->after('quantity')->comment('Volume in mL used per service (e.g., 2 mL per service)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_treatment_products', function (Blueprint $table) {
            $table->dropColumn('volume_used_per_service');
        });
    }
};
