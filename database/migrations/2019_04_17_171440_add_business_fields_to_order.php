<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessFieldsToOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_business')->default(false)->after('is_payment_received');
            $table->string('business_name')->after('email')->nullable();
            $table->string('business_tax_number')->after('business_name')->nullable();
            $table->string('business_address_line_one')->after('business_tax_number')->nullable();
            $table->string('business_address_line_two')->after('business_address_line_one')->nullable();
            $table->string('business_address_state_province')->after('business_address_line_two')->nullable();
            $table->string('business_address_city')->after('business_address_state_province')->nullable();
            $table->string('business_address_code')->after('business_address_city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_business',
                'business_name',
                'business_tax_number',
                'business_address_line_one',
                'business_address_line_two',
                'business_address_state_province',
                'business_address_city',
                'business_address_code',
            ]);
        });
    }
}
