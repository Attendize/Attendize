<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnswerOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answer_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('question_answer_id')->index();
            $table->unsignedInteger('question_option_id')->index();
            $table->unsignedInteger('option_id')->index();
            $table->decimal('price', 13, 2)->default(0.00);
            $table->foreign('question_answer_id')->references('id')->on('question_answers');
            $table->foreign('question_option_id')->references('id')->on('question_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('answer_options');
    }
}
