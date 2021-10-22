<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountsFieldToEventAccessCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_access_codes', function (Blueprint $table) {
            $table->unsignedInteger('usage_count')->default(0)->after('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_access_codes', function (Blueprint $table) {
            $table->dropColumn('usage_count');
        });
    }
}
