<?php

namespace App\Http\Controllers;

use App\Models\EventSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Tarea 4 · Publicar o retirar una sesión del programa público.
 *
 * Se envía el estado deseado (true/false) en lugar de "alternar":
 * así repetir la misma petición no cambia el resultado (operación idempotente, AC3).
 */
class SessionPublicationController extends Controller
{
    public function update(Request $request, int $eventId, int $sessionId): RedirectResponse
    {
        $data = $request->validate(
            ['is_published' => ['required', 'boolean']],
            ['is_published.required' => 'Indica si la sesión se publica o se retira.'],
        );

        // TODO T4-2: localiza la sesión SOLO dentro de la cuenta del usuario y del evento de la ruta
        // (si no, 404), cambia is_published, guarda y vuelve atrás con un mensaje 'success'.
        abort(501, 'TODO T4-2: falta implementar la publicación.');
    }
}
