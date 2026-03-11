<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'early_checkout_minutes')) {
                $table->integer('early_checkout_minutes')->default(0)->after('out_time');
            }
            if (!Schema::hasColumn('attendance', 'overtime_hours')) {
                $table->decimal('overtime_hours', 5, 2)->default(0)->after('early_checkout_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['early_checkout_minutes', 'overtime_hours']);
        });
    }
};
