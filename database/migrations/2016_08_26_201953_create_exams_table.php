<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_id');
            $table->foreign('client_id')->references('client_id')->on('clients');
            $table->string('title', 255);
            $table->string('description');
            $table->smallInteger('duration')->unsigned();
            $table->smallInteger('attempts')->unsigned();
            $table->smallInteger('size')->unsigned();
            $table->smallInteger('cutoff')->unsigned();
            $table->boolean('randomize')->default(true);
            $table->timestamps();
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
        Schema::drop('exams');
    }
}
