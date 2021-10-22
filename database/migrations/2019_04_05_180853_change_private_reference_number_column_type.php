<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePrivateReferenceNumberColumnType extends Migration
{
    /**
     * Run the migrations.
     * Change Private Reference Number from INT to VARCHAR ColumnType
     * and increases the character count to 15
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->string('private_reference_number', 15)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('attendees', function ($table) {
        //     $table->integer('private_reference_number')->change();
        // });
    }
}
