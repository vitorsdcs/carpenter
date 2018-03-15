<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('exam_id')->unsigned();
            $table->foreign('exam_id')->references('id')->on('exams');
            $table->string('examinable_type');
            $table->integer('examinable_id');
            $table->dateTimeTz('started_at')->nullable();
            $table->dateTimeTz('finished_at')->nullable();
            $table->smallInteger('score')->unsigned()->default(0);
            $table->boolean('cheat')->default(false);
            $table->boolean('expired')->default(false);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('exam_submissions');
    }
}
