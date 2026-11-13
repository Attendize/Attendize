<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventSession;
use App\Models\User;
use Tests\TestCase;
use Tests\UsesTestDatabase;

/** Tarea 4 · Publicar y retirar sesiones (proporcionado). */
class SessionPublicationTest extends TestCase
{
    use UsesTestDatabase;

    public function test_el_organizador_publica_y_retira_una_sesion_de_su_evento(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['account_id' => $user->account_id]);
        $session = EventSession::factory()->forEvent($event)->create();
        $url = "/events/{$event->id}/sessions/{$session->id}/publication";

        $this->actingAs($user)->patch($url, ['is_published' => true])->assertSessionHas('success');
        $this->assertTrue($session->fresh()->is_published);

        // Repetir la misma petición no cambia el resultado (idempotente)
        $this->actingAs($user)->patch($url, ['is_published' => true]);
        $this->assertTrue($session->fresh()->is_published);

        $this->actingAs($user)->patch($url, ['is_published' => false]);
        $this->assertFalse($session->fresh()->is_published);
    }

    public function test_no_puedo_publicar_sesiones_de_otra_cuenta(): void
    {
        $user = User::factory()->create();
        $otherEvent = Event::factory()->create();
        $session = EventSession::factory()->forEvent($otherEvent)->create();

        $this->actingAs($user)
            ->patch("/events/{$otherEvent->id}/sessions/{$session->id}/publication", ['is_published' => true])
            ->assertNotFound();

        $this->assertFalse($session->fresh()->is_published);
    }

    public function test_la_sesion_debe_pertenecer_al_evento_de_la_ruta(): void
    {
        $user = User::factory()->create();
        $eventA = Event::factory()->create(['account_id' => $user->account_id]);
        $eventB = Event::factory()->create(['account_id' => $user->account_id]);
        $sessionOfB = EventSession::factory()->forEvent($eventB)->create();

        $this->actingAs($user)
            ->patch("/events/{$eventA->id}/sessions/{$sessionOfB->id}/publication", ['is_published' => true])
            ->assertNotFound();
    }

    public function test_el_estado_es_obligatorio_y_booleano(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['account_id' => $user->account_id]);
        $session = EventSession::factory()->forEvent($event)->create();

        $this->actingAs($user)
            ->patch("/events/{$event->id}/sessions/{$session->id}/publication", ['is_published' => 'quizá'])
            ->assertSessionHasErrors('is_published');
    }
}
