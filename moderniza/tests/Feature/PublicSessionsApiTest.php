<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventSession;
use Tests\TestCase;
use Tests\UsesTestDatabase;

/** Tarea 4 · API REST GET /api/events/{id}/sessions (A5, AC6). */
class PublicSessionsApiTest extends TestCase
{
    use UsesTestDatabase;

    /** EJEMPLO RESUELTO: forma de la respuesta y datos que NO deben publicarse. */
    public function test_devuelve_json_solo_con_los_campos_publicos(): void
    {
        $event = Event::factory()->create(['is_live' => true, 'title' => 'TechFest Valencia']);
        EventSession::factory()->forEvent($event)->create(['is_published' => true, 'title' => 'Keynote']);

        $this->getJson("/api/events/{$event->id}/sessions")
            ->assertOk()
            ->assertJsonPath('event.title', 'TechFest Valencia')
            ->assertJsonPath('data.0.title', 'Keynote')
            ->assertJsonStructure(['data' => [['id', 'title', 'description', 'starts_at', 'ends_at', 'room', 'capacity']]])
            ->assertJsonMissingPath('data.0.account_id')
            ->assertJsonMissingPath('data.0.event_id')
            ->assertJsonMissingPath('data.0.is_published');
    }

    public function test_solo_devuelve_sesiones_publicadas_y_ordenadas_por_inicio(): void
    {
        // TODO T4-5: crea un evento publicado con 3 sesiones: una en borrador y dos publicadas
        // (la de las 12:00 creada ANTES que la de las 09:00). Comprueba con assertJsonCount(2, 'data')
        // y assertJsonPath que data.0 es la de las 09:00 y data.1 la de las 12:00.
        $this->markTestIncomplete('TODO T4-5');
    }

    public function test_un_evento_no_publicado_en_attendize_responde_404(): void
    {
        $event = Event::factory()->create(['is_live' => false]);
        EventSession::factory()->forEvent($event)->create(['is_published' => true]);

        $this->getJson("/api/events/{$event->id}/sessions")
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    public function test_un_evento_inexistente_responde_404(): void
    {
        $this->getJson('/api/events/999999/sessions')->assertNotFound();
    }

    public function test_la_pagina_publica_del_programa_no_requiere_login(): void
    {
        $event = Event::factory()->create(['is_live' => true]);

        $this->get("/eventos/{$event->id}/programa")
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Public/Program')->where('event.id', $event->id));

        $hidden = Event::factory()->create(['is_live' => false]);
        $this->get("/eventos/{$hidden->id}/programa")->assertNotFound();
    }
}
