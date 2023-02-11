<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the new file type question
        DB::table('question_types')->insert(
            array(
                'alias' => 'file_single',
                'name' => 'File upload',
                'has_options' => 0,
                'allow_multiple' => 1
            )
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the new question type
        DB::delete('DELETE FROM question_types WHERE alias=\'file_single\'');
    }
}
