<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTGTsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_g_ts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('teacher_subject_id')->nullable();
            $table->integer('numeric');
            $table->boolean('sub');
            $table->foreign('timetable_id')->references('id')->on('timetable');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('teacher_subject_id')->references('id')->on('teacher_subject');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_g_ts');
    }
}
