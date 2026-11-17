<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tarea 4 · Cambio de requisito "sin solapes en la sala".
 *
 * La comprobación busca sesiones del mismo evento y sala por horario.
 * Este índice acelera esa consulta. Es reversible: down() lo elimina
 * sin tocar los datos existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_sessions', function (Blueprint $table) {
            $table->index(['event_id', 'room', 'starts_at'], 'event_sessions_event_room_start_index');
        });
    }

    public function down(): void
    {
        Schema::table('event_sessions', function (Blueprint $table) {
            $table->dropIndex('event_sessions_event_room_start_index');
        });
    }
};
