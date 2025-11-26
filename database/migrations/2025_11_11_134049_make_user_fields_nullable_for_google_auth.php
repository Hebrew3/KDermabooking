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
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable()->change();
            $table->string('mobile_number')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->date('birth_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable(false)->change();
            $table->string('mobile_number')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->date('birth_date')->nullable(false)->change();
        });
    }
};
