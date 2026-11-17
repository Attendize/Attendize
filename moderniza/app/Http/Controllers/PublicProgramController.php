<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Página pública del programa (proporcionado).
 * Solo envía a React el evento; las sesiones las pide React a la API REST.
 */
class PublicProgramController extends Controller
{
    public function show(int $eventId): Response
    {
        $event = Event::query()->where('is_live', true)->whereKey($eventId)->firstOrFail();

        return Inertia::render('Public/Program', [
            'event' => ['id' => $event->id, 'title' => $event->title],
        ]);
    }
}
