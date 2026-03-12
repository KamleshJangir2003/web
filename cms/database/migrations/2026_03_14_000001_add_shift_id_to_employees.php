<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'shift_id')) {
                $table->unsignedBigInteger('shift_id')->nullable()->after('shift');
                $table->foreign('shift_id')->references('id')->on('shift_types')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'shift_id')) {
                $table->dropForeign(['shift_id']);
                $table->dropColumn('shift_id');
            }
        });
    }
};
