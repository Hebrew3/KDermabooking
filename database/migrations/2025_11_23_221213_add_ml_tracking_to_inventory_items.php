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
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('volume_per_container', 10, 2)->nullable()->after('content_unit')->comment('Volume in mL per container (e.g., 20 mL per bottle)');
            $table->decimal('total_volume_ml', 12, 2)->default(0)->after('volume_per_container')->comment('Total volume in mL in stock');
            $table->decimal('remaining_volume_per_container', 10, 2)->nullable()->after('total_volume_ml')->comment('Remaining volume in mL in the current open container');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn(['volume_per_container', 'total_volume_ml', 'remaining_volume_per_container']);
        });
    }
};
