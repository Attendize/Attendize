<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Tests\TestCase;
use Tests\UsesTestDatabase;

/**
 * Tarea 3: crear una sesión en un evento propio.
 * Criterios A2 (caso correcto), A3 (datos inválidos) y A4 (otra cuenta).
 */
class CreateEventSessionTest extends TestCase
{
    use UsesTestDatabase;

    /** A2 · Caso correcto (EJEMPLO RESUELTO). */
    public function test_el_organizador_crea_una_sesion_en_su_evento(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['account_id' => $user->account_id]);

        $response = $this->actingAs($user)->post("/events/{$event->id}/sessions", $this->validData());

        $response->assertRedirect("/events/{$event->id}/sessions");
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('event_sessions', [
            'event_id' => $event->id,
            'account_id' => $user->account_id,
            'title' => 'Introducción a React',
            'room' => 'Aula 2.1',
            'capacity' => 30,
        ]);
    }

    /** A3 · Datos inválidos: no se crea nada y se informa de cada error. */
    public function test_los_datos_invalidos_se_rechazan(): void
    {
        // TODO T3-7: prepara un usuario y un evento de SU cuenta, envía datos inválidos
        // (título vacío, fin anterior al inicio, aforo 0) y comprueba:
        // - assertSessionHasErrors(['title', 'ends_at', 'capacity'])
        // - assertDatabaseCount('event_sessions', 0)
        $this->markTestIncomplete('TODO T3-7');
    }

    /** A4 · Sin permiso: un usuario NO puede crear sesiones en el evento de otra cuenta. */
    public function test_no_puedo_crear_sesiones_en_un_evento_de_otra_cuenta(): void
    {
        // TODO T3-7: crea un usuario y un evento de OTRA cuenta, intenta crear la sesión
        // y comprueba que la respuesta es 404 (assertNotFound) y que no hay filas.
        $this->markTestIncomplete('TODO T3-7');
    }

    /** A4 · Tampoco se puede ver el programa de un evento ajeno escribiendo la URL. */
    public function test_no_puedo_abrir_el_programa_de_un_evento_de_otra_cuenta(): void
    {
        $user = User::factory()->create();
        $otherEvent = Event::factory()->create();

        $this->actingAs($user)->get("/events/{$otherEvent->id}/sessions")->assertNotFound();
        $this->actingAs($user)->get("/events/{$otherEvent->id}/sessions/create")->assertNotFound();
    }

    /** Seguridad: aunque el navegador envíe account_id/event_id, el servidor los ignora. */
    public function test_el_servidor_ignora_account_id_y_event_id_enviados_por_el_navegador(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['account_id' => $user->account_id]);
        $otherEvent = Event::factory()->create();

        $this->actingAs($user)->post("/events/{$event->id}/sessions", array_merge($this->validData(), [
            'account_id' => $otherEvent->account_id,
            'event_id' => $otherEvent->id,
        ]));

        $this->assertDatabaseHas('event_sessions', ['event_id' => $event->id, 'account_id' => $user->account_id]);
        $this->assertDatabaseMissing('event_sessions', ['event_id' => $otherEvent->id]);
    }

    /** Un visitante sin sesión iniciada no puede crear sesiones. */
    public function test_un_visitante_no_puede_crear_sesiones(): void
    {
        $event = Event::factory()->create();

        $this->post("/events/{$event->id}/sessions", $this->validData())->assertRedirect('/login');
        $this->assertDatabaseCount('event_sessions', 0);
    }

    /** @return array<string, mixed> */
    private function validData(): array
    {
        return [
            'title' => 'Introducción a React',
            'description' => 'Sesión ficticia de TechFest',
            'starts_at' => '2027-03-12 10:00:00',
            'ends_at' => '2027-03-12 11:00:00',
            'room' => 'Aula 2.1',
            'capacity' => 30,
        ];
    }
}
