<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class OrganiserPageDesignUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisers', function (Blueprint $table) {
            $table->boolean('show_twitter_widget')->default(false);
            $table->boolean('show_facebook_widget')->default(false);

            $table->string('page_header_bg_color', 20)->default('#76a867');
            $table->string('page_bg_color', 20)->default('#EEEEEE');
            $table->string('page_text_color', 20)->default('#FFFFFF');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisers', function (Blueprint $table) {
            $table->dropColumn('show_twitter_widget');
            $table->dropColumn('show_facebook_widget');
            $table->dropColumn('page_header_bg_color');
            $table->dropColumn('page_bg_color');
            $table->dropColumn('page_text_color');
        });
    }
}
