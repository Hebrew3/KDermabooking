<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include nurse and aesthetician roles (staff will be removed in next migration)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'staff', 'nurse', 'aesthetician', 'client') DEFAULT 'client'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        // First, update any nurse or aesthetician users to staff
        DB::statement("UPDATE users SET role = 'staff' WHERE role IN ('nurse', 'aesthetician')");
        
        // Then modify the enum back
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'staff', 'client') DEFAULT 'client'");
    }
};
