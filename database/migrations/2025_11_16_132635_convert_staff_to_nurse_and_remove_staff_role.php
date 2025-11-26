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
        // First, convert all existing staff users to nurse role
        DB::statement("UPDATE users SET role = 'nurse' WHERE role = 'staff'");
        
        // Then, remove staff from the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'nurse', 'aesthetician', 'client') DEFAULT 'client'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add staff back to enum first
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'staff', 'nurse', 'aesthetician', 'client') DEFAULT 'client'");
        
        // Convert nurses back to staff (this is a best guess - you may want to adjust this)
        // Note: This will convert ALL nurses to staff, which may not be desired
        // DB::statement("UPDATE users SET role = 'staff' WHERE role = 'nurse'");
    }
};
