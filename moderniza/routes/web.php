<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSessionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/events');

// Acceso (mismos usuarios que Attendize)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');
});

// Zona privada: TODAS estas rutas exigen usuario autenticado.
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Ejemplo resuelto: mis eventos
    Route::get('/events', [EventController::class, 'index'])->name('events.index');

    // Tarea 3: programa de sesiones de un evento propio
    Route::get('/events/{eventId}/sessions', [EventSessionController::class, 'index'])
        ->whereNumber('eventId')->name('events.sessions.index');
    Route::get('/events/{eventId}/sessions/create', [EventSessionController::class, 'create'])
        ->whereNumber('eventId')->name('events.sessions.create');

    // TODO T3-5: ruta POST /events/{eventId}/sessions -> EventSessionController@store
    // con nombre 'events.sessions.store' (mira las rutas GET de arriba).
});
