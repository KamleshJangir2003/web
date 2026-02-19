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
        // Update existing values to match new enum
        DB::table('employees')
            ->where('employee_status', 'hired')
            ->update(['employee_status' => 'active']);
        
        DB::table('employees')
            ->where('employee_status', 'inactive')
            ->update(['employee_status' => 'on_hold']);
        
        // Now modify the enum
        DB::statement("ALTER TABLE employees MODIFY COLUMN employee_status ENUM('active', 'resigned', 'terminated', 'absconding', 'notice_period', 'left', 'on_hold') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE employees MODIFY COLUMN employee_status ENUM('hired', 'active', 'inactive') DEFAULT 'hired'");
    }
};
