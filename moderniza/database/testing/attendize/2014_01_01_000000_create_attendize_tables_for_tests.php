<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SOLO PARA PRUEBAS (base moderniza_test).
 *
 * Reproduce las tablas de Attendize que usa Moderniza, con los MISMOS
 * tipos de clave (INT UNSIGNED), pero solo con las columnas necesarias.
 * En la base real "attendize" estas tablas ya existen y NO se crean.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $t) {
            $t->increments('id');
            $t->string('first_name');
            $t->string('last_name');
            $t->string('email');
            $t->nullableTimestamps();
            $t->softDeletes();
        });

        Schema::create('users', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('account_id')->index();
            $t->nullableTimestamps();
            $t->softDeletes();
            $t->string('first_name')->nullable();
            $t->string('last_name')->nullable();
            $t->string('email');
            $t->string('password');
            $t->string('remember_token', 100)->nullable();
            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        Schema::create('events', function (Blueprint $t) {
            $t->increments('id');
            $t->string('title');
            $t->text('description')->nullable();
            $t->dateTime('start_date')->nullable();
            $t->dateTime('end_date')->nullable();
            $t->boolean('is_live')->default(false);
            $t->unsignedInteger('account_id')->index();
            $t->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $t->nullableTimestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('users');
        Schema::dropIfExists('accounts');
    }
};
