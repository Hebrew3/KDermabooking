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
        // Simple approach: just update existing data to use capitalized format
        if (Schema::hasTable('staff_schedules')) {
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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to lowercase format
        if (Schema::hasTable('staff_schedules')) {
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
        }
    }
};
