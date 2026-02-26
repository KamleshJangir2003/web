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
        Schema::table('interns', function (Blueprint $table) {
            $table->date('completion_date')->nullable()->after('end_date');
            $table->string('performance_rating')->nullable()->after('completion_date');
            $table->text('completion_remarks')->nullable()->after('performance_rating');
            $table->string('certificate_path')->nullable()->after('completion_remarks');
            $table->date('cancellation_date')->nullable()->after('certificate_path');
            $table->text('cancellation_reason')->nullable()->after('cancellation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropColumn([
                'completion_date',
                'performance_rating',
                'completion_remarks',
                'certificate_path',
                'cancellation_date',
                'cancellation_reason'
            ]);
        });
    }
};
