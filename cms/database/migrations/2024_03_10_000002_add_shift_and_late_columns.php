<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'shift_id')) {
                $table->foreignId('shift_id')->nullable()->constrained('shift_types')->onDelete('set null');
            }
        });

        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'check_in')) {
                $table->dateTime('check_in')->nullable();
            }
            if (!Schema::hasColumn('attendance', 'check_out')) {
                $table->dateTime('check_out')->nullable();
            }
            if (!Schema::hasColumn('attendance', 'date')) {
                $table->date('date')->nullable();
            }
            if (!Schema::hasColumn('attendance', 'late_minutes')) {
                $table->integer('late_minutes')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['check_in', 'check_out', 'date', 'late_minutes']);
        });
    }
};
