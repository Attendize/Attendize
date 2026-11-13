<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * EJEMPLO RESUELTO (proporcionado por el profesorado).
 *
 * Recorrido de LECTURA completo: React -> Inertia -> Laravel -> MySQL.
 * Úsalo como modelo para la Tarea 3.
 */
class EventController extends Controller
{
    public function index(Request $request): Response
    {
        // 1. La cuenta sale del usuario autenticado, nunca del navegador.
        $accountId = $request->user()->account_id;

        // 2. Consulta a MySQL (tabla "events" de Attendize) filtrada por cuenta.
        $events = Event::query()
            ->ofAccount($accountId)
            ->orderByDesc('start_date')
            ->get(['id', 'title', 'start_date', 'end_date', 'is_live']);

        // 3. Solo se envían a React los datos que necesita la vista.
        return Inertia::render('Events/Index', [
            'events' => $events->map(fn (Event $event) => [
                'id' => $event->id,
                'title' => $event->title,
                'start_date' => $event->start_date?->format('d/m/Y H:i'),
                'end_date' => $event->end_date?->format('d/m/Y H:i'),
                'is_live' => $event->is_live,
            ]),
        ]);
    }
}
