<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventSession;
use App\Models\User;
use Tests\TestCase;
use Tests\UsesTestDatabase;

/**
 * Tarea 4 · Cambio de requisito: sin solapes en la misma sala.
 * Sesión existente en todas las pruebas: Aula 1, 10:00-11:00.
 */
class SessionOverlapTest extends TestCase
{
    use UsesTestDatabase;

    private User $user;

    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->event = Event::factory()->create(['account_id' => $this->user->account_id]);
        EventSession::factory()->forEvent($this->event)->create([
            'room' => 'Aula 1',
            'starts_at' => '2027-03-12 10:00:00',
            'ends_at' => '2027-03-12 11:00:00',
        ]);
    }

    /** EJEMPLO RESUELTO: 10:30-11:30 en Aula 1 se solapa -> error en "room" y no se crea. */
    public function test_rechaza_una_sesion_que_se_solapa_en_la_misma_sala(): void
    {
        $this->actingAs($this->user)
            ->post($this->url(), $this->data('Aula 1', '10:30', '11:30'))
            ->assertSessionHasErrors('room');

        $this->assertDatabaseCount('event_sessions', 1);
    }

    /** Una sesión que contiene por completo a otra también se solapa. */
    public function test_rechaza_una_sesion_que_contiene_a_otra(): void
    {
        $this->actingAs($this->user)
            ->post($this->url(), $this->data('Aula 1', '09:00', '12:00'))
            ->assertSessionHasErrors('room');

        $this->assertDatabaseCount('event_sessions', 1);
    }

    /** Caso límite: empezar justo cuando termina la otra NO es solape. */
    public function test_permite_una_sesion_que_empieza_cuando_termina_la_anterior(): void
    {
        // TODO T4-5: crea en Aula 1 una sesión de 11:00 a 12:00 y comprueba que
        // NO hay errores (assertSessionHasNoErrors) y que hay 2 filas en event_sessions.
        $this->markTestIncomplete('TODO T4-5');
    }

    /** Mismo horario en OTRA sala sí se permite. */
    public function test_permite_el_mismo_horario_en_otra_sala(): void
    {
        // TODO T4-5: crea en "Aula 2" una sesión de 10:00 a 11:00 y comprueba que se guarda.
        $this->markTestIncomplete('TODO T4-5');
    }

    /** La regla es por evento: la misma sala en OTRO evento no cuenta. */
    public function test_permite_la_misma_sala_y_horario_en_otro_evento(): void
    {
        $otherEvent = Event::factory()->create(['account_id' => $this->user->account_id]);

        $this->actingAs($this->user)
            ->post("/events/{$otherEvent->id}/sessions", $this->data('Aula 1', '10:00', '11:00'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('event_sessions', 2);
    }

    private function url(): string
    {
        return "/events/{$this->event->id}/sessions";
    }

    /** @return array<string, mixed> */
    private function data(string $room, string $start, string $end): array
    {
        return [
            'title' => 'Sesión de prueba',
            'starts_at' => "2027-03-12 {$start}:00",
            'ends_at' => "2027-03-12 {$end}:00",
            'room' => $room,
            'capacity' => 20,
        ];
    }
}
