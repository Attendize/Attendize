<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventSessionRequest;
use App\Models\Event;
use App\Models\EventSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Programa de sesiones de un evento (núcleo de la Tarea 3).
 *
 * Recorrido de ESCRITURA: React (formulario) -> Inertia -> Laravel
 * (validación + autorización + regla de negocio) -> MySQL -> respuesta.
 */
class EventSessionController extends Controller
{
    /** Listado de sesiones de un evento propio (proporcionado). */
    public function index(Request $request, int $eventId): Response
    {
        $event = $this->findOwnEvent($request, $eventId);

        return Inertia::render('Sessions/Index', [
            'event' => ['id' => $event->id, 'title' => $event->title],
            'sessions' => $event->sessions()->get()->map(fn (EventSession $session) => [
                'id' => $session->id,
                'title' => $session->title,
                'starts_at' => $session->starts_at->format('d/m/Y H:i'),
                'ends_at' => $session->ends_at->format('H:i'),
                'room' => $session->room,
                'capacity' => $session->capacity,
            ]),
        ]);
    }

    /** Formulario de nueva sesión (proporcionado). */
    public function create(Request $request, int $eventId): Response
    {
        $event = $this->findOwnEvent($request, $eventId);

        return Inertia::render('Sessions/Create', [
            'event' => ['id' => $event->id, 'title' => $event->title],
        ]);
    }

    /** Guarda una sesión nueva (criterios A2, A3 y A4). */
    public function store(StoreEventSessionRequest $request, int $eventId): RedirectResponse
    {
        // TODO T3-5: implementa la creación de la sesión.
        // 1. Localiza el evento con findOwnEvent() (si no es de tu cuenta -> 404).
        // 2. Crea la sesión SOLO con los datos validados: $request->validated().
        // 3. Asigna account_id y event_id EN EL SERVIDOR (no desde el formulario).
        // 4. Guarda y redirige al listado con el mensaje flash 'success'.
        abort(501, 'TODO T3-5: falta implementar store().');
    }

    /**
     * Regla de autorización: el evento debe pertenecer a la cuenta
     * del usuario autenticado. Si no, se responde 404 (no se revela
     * que el evento existe en otra cuenta).
     */
    private function findOwnEvent(Request $request, int $eventId): Event
    {
        // TODO T3-4: busca el evento por id DENTRO de la cuenta del usuario (criterio A4).
        // Pista: mira cómo filtra EventController::index() y usa firstOrFail().
        abort(501, 'TODO T3-4: falta implementar findOwnEvent().');
    }
}
