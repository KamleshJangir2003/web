<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index(['user_type', 'is_approved', 'action_status'], 'idx_employee_status');
            $table->index(['hired_status', 'employee_status'], 'idx_hired_status');
            $table->index('department');
            $table->index('platform');
            $table->index('email');
        });

        Schema::table('employee_documents', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index('document_type');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('callbacks', function (Blueprint $table) {
            $table->index(['callback_date', 'status'], 'idx_callback_status');
        });

        Schema::table('interviews', function (Blueprint $table) {
            $table->index('status');
            $table->index('result');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_employee_status');
            $table->dropIndex('idx_hired_status');
            $table->dropIndex(['department']);
            $table->dropIndex(['platform']);
            $table->dropIndex(['email']);
        });

        Schema::table('employee_documents', function (Blueprint $table) {
            $table->dropIndex('idx_user_status');
            $table->dropIndex(['document_type']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('callbacks', function (Blueprint $table) {
            $table->dropIndex('idx_callback_status');
        });

        Schema::table('interviews', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['result']);
        });
    }
};
