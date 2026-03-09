<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shift_types', function (Blueprint $table) {
            $table->id();
            $table->string('shift_name');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('late_after')->default(0)->comment('Minutes after start_time');
            $table->timestamps();
        });

        // Insert default shifts
        DB::table('shift_types')->insert([
            [
                'shift_name' => 'Day Shift',
                'start_time' => '09:30:00',
                'end_time' => '18:30:00',
                'late_after' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'shift_name' => 'Night Shift',
                'start_time' => '19:30:00',
                'end_time' => '05:10:00',
                'late_after' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('shift_types');
    }
};
