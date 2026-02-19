<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            if (!Schema::hasColumn('interviews', 'current_ctc')) {
                $table->decimal('current_ctc', 10, 2)->nullable()->after('result');
            }
            if (!Schema::hasColumn('interviews', 'in_hand_salary')) {
                $table->decimal('in_hand_salary', 10, 2)->nullable()->after('current_ctc');
            }
        });
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn(['current_ctc', 'in_hand_salary']);
        });
    }
};
