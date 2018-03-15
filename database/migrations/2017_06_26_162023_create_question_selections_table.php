<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_selections', function (Blueprint $table) {
            $table->integer('exam_submission_id')->unsigned();
            $table->foreign('exam_submission_id')->references('id')->on('exam_submissions');
            $table->integer('question_submission_id')->unsigned();
            $table->foreign('question_submission_id')->references('id')->on('question_submissions');
            $table->integer('question_option_submission_id')->unsigned();
            $table->foreign('question_option_submission_id')->references('id')->on('question_option_submissions');
            $table->dateTimeTz('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('question_selections');
    }
}
