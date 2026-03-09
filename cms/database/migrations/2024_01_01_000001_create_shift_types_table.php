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
            $table->integer('late_after')->default(0); // minutes after start_time
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift_types');
    }
};
