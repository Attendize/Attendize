<?php

use App\Http\Controllers\PublicProgramController;
use App\Http\Controllers\SessionPublicationController;
use Illuminate\Support\Facades\Route;

/*
| Rutas web de la Tarea 4 (cargadas desde bootstrap/app.php junto a web.php).
*/

// Público: programa de un evento publicado en Attendize
Route::get('/eventos/{eventId}/programa', [PublicProgramController::class, 'show'])
    ->whereNumber('eventId')
    ->name('public.program');

// Privado: publicar o retirar una sesión de un evento propio
Route::middleware('auth')->group(function () {
    Route::patch('/events/{eventId}/sessions/{sessionId}/publication', [SessionPublicationController::class, 'update'])
        ->whereNumber(['eventId', 'sessionId'])
        ->name('events.sessions.publication');
});
