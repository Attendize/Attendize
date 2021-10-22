<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class OrderPageUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('questions_collection_type', 10)->default('buyer'); // buyer or attendee
            $table->integer('checkout_timeout_after')->default(8); // timeout in mins for checkout
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['checkout_timeout_after', 'questions_collection_type']);
        });
    }
}
