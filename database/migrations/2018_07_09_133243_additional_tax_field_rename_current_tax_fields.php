<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AdditionalTaxFieldRenameCurrentTaxFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisers', function (Blueprint $table) {
            $table->boolean('charge_tax')->default(0);
        });

        Schema::table('organisers', function (Blueprint $table) {
            $table->renameColumn('taxname', 'tax_name');
        });

        Schema::table('organisers', function (Blueprint $table) {
            $table->renameColumn('taxvalue', 'tax_value');
        });

        Schema::table('organisers', function (Blueprint $table) {
            $table->renameColumn('taxid', 'tax_id');
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
            $table->dropColumn('charge_tax');
        });

        Schema::table('organisers', function (Blueprint $table) {
            $table->renameColumn('tax_name', 'taxname');
        });

        Schema::table('organisers', function (Blueprint $table) {
            $table->renameColumn('tax_value', 'taxvalue');
        });

        Schema::table('organisers', function (Blueprint $table) {
            $table->renameColumn('tax_id', 'taxid');
        });
    }
}
