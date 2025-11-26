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
        // Check if table exists
        if (!Schema::hasTable('staff_schedules')) {
            return;
        }

        // Check if day_of_week column exists
        if (!Schema::hasColumn('staff_schedules', 'day_of_week')) {
            return;
        }

        // Update existing lowercase day names to capitalized format
        $dayMappings = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday', 
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];

        foreach ($dayMappings as $oldDay => $newDay) {
            DB::table('staff_schedules')
                ->where('day_of_week', $oldDay)
                ->update(['day_of_week' => $newDay]);
        }

        // Drop unique constraint first if it exists
        try {
            Schema::table('staff_schedules', function (Blueprint $table) {
                $table->dropUnique(['staff_id', 'day_of_week']);
            });
        } catch (Exception $e) {
            // Constraint might not exist, continue
        }

        // For MySQL, we need to modify the column directly using raw SQL
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE staff_schedules MODIFY COLUMN day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL");
        }

        // Recreate the unique constraint
        try {
            Schema::table('staff_schedules', function (Blueprint $table) {
                $table->unique(['staff_id', 'day_of_week']);
            });
        } catch (Exception $e) {
            // Constraint might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to lowercase format
        $dayMappings = [
            'Monday' => 'monday',
            'Tuesday' => 'tuesday', 
            'Wednesday' => 'wednesday',
            'Thursday' => 'thursday',
            'Friday' => 'friday',
            'Saturday' => 'saturday',
            'Sunday' => 'sunday'
        ];

        foreach ($dayMappings as $oldDay => $newDay) {
            DB::table('staff_schedules')
                ->where('day_of_week', $oldDay)
                ->update(['day_of_week' => $newDay]);
        }

        // Drop and recreate with lowercase values
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->dropUnique(['staff_id', 'day_of_week']);
            $table->dropColumn('day_of_week');
        });

        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->after('staff_id');
            $table->unique(['staff_id', 'day_of_week']);
        });
    }
};
