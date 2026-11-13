<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Tests\TestCase;
use Tests\UsesTestDatabase;

/**
 * EJEMPLO RESUELTO (proporcionado): pruebas del listado "Mis eventos".
 * Fíjate en la estructura: preparar datos -> actuar -> comprobar.
 */
class ExampleEventsTest extends TestCase
{
    use UsesTestDatabase;

    public function test_un_visitante_sin_sesion_va_al_login(): void
    {
        $this->get('/events')->assertRedirect('/login');
    }

    public function test_el_login_funciona_con_un_usuario_de_attendize(): void
    {
        $user = User::factory()->create(['email' => 'org@techfest.test']);

        $this->post('/login', ['email' => 'org@techfest.test', 'password' => 'password'])
            ->assertRedirect('/events');

        $this->assertAuthenticatedAs($user);
    }

    public function test_solo_veo_los_eventos_de_mi_cuenta(): void
    {
        $user = User::factory()->create();
        $mine = Event::factory()->create(['account_id' => $user->account_id, 'title' => 'TechFest Valencia']);
        $other = Event::factory()->create(['title' => 'Evento de otra cuenta']);

        $this->actingAs($user)
            ->get('/events')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Events/Index')
                ->has('events', 1)
                ->where('events.0.id', $mine->id));
    }
}
