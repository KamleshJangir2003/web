<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->decimal('working_hours', 5, 2)->nullable();
            $table->enum('status', ['Present', 'Late', 'Half Day', 'Absent'])->default('Absent');
            $table->foreignId('shift_id')->nullable()->constrained('shift_types')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['employee_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
