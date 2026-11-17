<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicSessionResource;
use App\Models\Event;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * API REST pública · Tarea 4 (R6, A5, AC6)
 *
 * GET /api/events/{eventId}/sessions
 *   200 → sesiones PUBLICADAS del evento, ordenadas por inicio.
 *   404 → el evento no existe o no está publicado en Attendize (is_live = 0).
 * No requiere autenticación: es información pública del programa.
 */
class PublicSessionController extends Controller
{
    public function index(int $eventId): AnonymousResourceCollection
    {
        // TODO T4-3: busca el evento PUBLICADO (is_live = true) o responde 404 (firstOrFail),
        // y devuelve SOLO sus sesiones publicadas (is_published = true) ordenadas por starts_at,
        // usando PublicSessionResource::collection(...). Añade el evento con ->additional(...).
        abort(501, 'TODO T4-3: falta implementar la API.');
    }
}
