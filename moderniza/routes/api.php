<?php

use App\Http\Controllers\Api\PublicSessionController;
use Illuminate\Support\Facades\Route;

/*
| API REST de Moderniza (prefijo /api). Respuestas siempre en JSON.
| Documentación: docs/api/sesiones-publicadas.md
*/
Route::get('/events/{eventId}/sessions', [PublicSessionController::class, 'index'])
    ->whereNumber('eventId')
    ->middleware('throttle:60,1')
    ->name('api.events.sessions.index');
