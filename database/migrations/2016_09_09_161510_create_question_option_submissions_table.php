<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionOptionSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_option_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_submission_id')->unsigned();
            $table->foreign('question_submission_id')->references('id')->on('question_submissions');
            $table->integer('question_option_id')->unsigned();
            $table->foreign('question_option_id')->references('id')->on('question_options');
            $table->string('answer');
            $table->boolean('is_correct');
            $table->boolean('chosen')->default(false);
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
        Schema::drop('question_option_submissions');
    }
}
