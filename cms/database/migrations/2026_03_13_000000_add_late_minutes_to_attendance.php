<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'late_minutes')) {
                $table->integer('late_minutes')->default(0)->after('in_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasColumn('attendance', 'late_minutes')) {
                $table->dropColumn('late_minutes');
            }
        });
    }
};
