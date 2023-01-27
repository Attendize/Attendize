<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionOptionsPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->decimal('price', 13, 2)->default(0.00);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('extras', 13, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('extras');
        });
    }
}
