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
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('induction_round', ['yes', 'no'])->nullable()->after('hired_status');
            $table->enum('training', ['yes', 'no'])->nullable()->after('induction_round');
            $table->integer('certification_period')->default(5)->after('training');
            $table->enum('action_status', ['selected', 'not_selected', 'reason', 'rejected'])->nullable()->after('certification_period');
            $table->text('action_reason')->nullable()->after('action_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['induction_round', 'training', 'certification_period', 'action_status', 'action_reason']);
        });
    }
};
