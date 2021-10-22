<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendeesQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Checkbox, dropdown, radio, text etc.
         */
        Schema::create('question_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias');
            $table->string('name');
            $table->boolean('has_options')->default(false);
            $table->boolean('allow_multiple')->default(false);
        });

        /**
         * The questions.
         */
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title', 255);
            $table->text('instructions');

            $table->unsignedInteger('question_type_id');
            $table->unsignedInteger('account_id')->index();

            $table->tinyInteger('is_required')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('question_type_id')->references('id')->on('question_types');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        /**
         * Used for the questions that allow options (checkbox, radio, dropdown).
         */
        Schema::create('question_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('question_id')->unsigned()->index();

            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        /**
         * Event / Question pivot table.
         */
        Schema::create('event_question', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned()->index();
            $table->integer('question_id')->unsigned()->index();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        /**
         * Question / Ticket pivot table.
         */
        Schema::create('question_ticket', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id')->unsigned()->index();
            $table->integer('ticket_id')->unsigned()->index();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'question_types',
            'questions',
            'question_options',
            'event_question',
            'question_ticket',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            Schema::drop($table);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
