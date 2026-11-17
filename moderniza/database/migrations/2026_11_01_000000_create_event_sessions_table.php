<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla nueva de Moderniza: sesiones del programa de un evento.
 *
 * Se crea en la MISMA base que Attendize, pero solo esta tabla es nueva.
 * Las tablas de Attendize (accounts, users, events...) no se tocan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();

            // TODO T3-1: claves externas hacia accounts e events (borrado en cascada).
            // ¡Ojo! En Attendize accounts.id y events.id son INT UNSIGNED
            // (increments), no BIGINT. foreignId() crea BIGINT y MySQL rechazaría
            // la clave externa. Usa unsignedInteger() + foreign().

            // TODO T3-1: columnas de la sesión: title (120), description (texto, opcional),
            // starts_at, ends_at, room (80) y capacity (entero sin signo).

            $table->boolean('is_published')->default(false);
            $table->timestamps();

            // TODO T3-1: índice para listar rápido las sesiones de un evento de una cuenta.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sessions');
    }
};
