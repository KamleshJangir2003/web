<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'in_time')) {
                $table->time('in_time')->nullable()->after('status');
            }
            if (!Schema::hasColumn('attendance', 'out_time')) {
                $table->time('out_time')->nullable()->after('in_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['in_time', 'out_time']);
        });
    }
};
