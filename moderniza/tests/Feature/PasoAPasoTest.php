<?php

namespace Tests\Feature;

use App\Http\Requests\StoreEventSessionRequest;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\UsesTestDatabase;

/**
 * Comprobaciones paso a paso de la Tarea 3 (proporcionado por el profesorado).
 *
 * No hay que modificar este archivo. Al terminar cada TODO, ejecutad su grupo:
 *
 *   docker compose exec moderniza php artisan test --group=t3-1
 *   docker compose exec moderniza php artisan test --group=t3-2
 *   ... hasta t3-5
 *
 * Si sale en verde, pasad al siguiente TODO. Si sale en rojo, leed el mensaje:
 * indica qué falta.
 */
class PasoAPasoTest extends TestCase
{
    use UsesTestDatabase;

    // ---------------------------------------------------------------- T3-1

    #[Group('t3-1')]
    public function test_t3_1_la_tabla_event_sessions_tiene_todas_las_columnas(): void
    {
        $columnas = ['account_id', 'event_id', 'title', 'description', 'starts_at', 'ends_at', 'room', 'capacity'];

        foreach ($columnas as $columna) {
            $this->assertTrue(
                Schema::hasColumn('event_sessions', $columna),
                "T3-1: falta la columna \"{$columna}\" en la migración."
            );
        }
    }

    #[Group('t3-1')]
    public function test_t3_1_hay_claves_externas_hacia_accounts_y_events(): void
    {
        $claves = collect(Schema::getForeignKeys('event_sessions'))
            ->mapWithKeys(fn (array $fk) => [implode(',', $fk['columns']) => $fk['foreign_table']]);

        $this->assertSame('accounts', $claves->get('account_id'), 'T3-1: falta la clave externa account_id → accounts.');
        $this->assertSame('events', $claves->get('event_id'), 'T3-1: falta la clave externa event_id → events.');
    }

    #[Group('t3-1')]
    public function test_t3_1_hay_un_indice_que_empieza_por_account_id_y_event_id(): void
    {
        $indices = collect(Schema::getIndexes('event_sessions'))
            ->filter(fn (array $i) => array_slice($i['columns'], 0, 2) === ['account_id', 'event_id']);

        $this->assertNotEmpty($indices, 'T3-1: falta el índice (account_id, event_id, starts_at).');
    }

    // ---------------------------------------------------------------- T3-2

    #[Group('t3-2')]
    public function test_t3_2_solo_se_asignan_en_masa_los_campos_del_formulario(): void
    {
        $fillable = (new EventSession)->getFillable();

        foreach (['title', 'description', 'starts_at', 'ends_at', 'room', 'capacity'] as $campo) {
            $this->assertContains($campo, $fillable, "T3-2: \"{$campo}\" debe estar en \$fillable.");
        }
        $this->assertNotContains('account_id', $fillable, 'T3-2: account_id NO debe estar en $fillable (lo pone el servidor).');
        $this->assertNotContains('event_id', $fillable, 'T3-2: event_id NO debe estar en $fillable (lo pone el servidor).');
    }

    #[Group('t3-2')]
    public function test_t3_2_las_fechas_y_el_aforo_se_convierten_de_tipo(): void
    {
        $sesion = new EventSession;
        $sesion->forceFill(['starts_at' => '2027-03-12 10:00:00', 'ends_at' => '2027-03-12 11:00:00', 'capacity' => '30']);

        $this->assertInstanceOf(CarbonInterface::class, $sesion->starts_at, 'T3-2: starts_at debe convertirse a fecha (datetime).');
        $this->assertInstanceOf(CarbonInterface::class, $sesion->ends_at, 'T3-2: ends_at debe convertirse a fecha (datetime).');
        $this->assertSame(30, $sesion->capacity, 'T3-2: capacity debe convertirse a entero (integer).');
    }

    #[Group('t3-2')]
    public function test_t3_2_un_evento_devuelve_sus_sesiones_ordenadas_por_inicio(): void
    {
        $event = Event::factory()->create();
        EventSession::factory()->create(['event_id' => $event->id, 'account_id' => $event->account_id,
            'title' => 'Segunda', 'starts_at' => '2027-03-12 12:00:00', 'ends_at' => '2027-03-12 13:00:00']);
        EventSession::factory()->create(['event_id' => $event->id, 'account_id' => $event->account_id,
            'title' => 'Primera', 'starts_at' => '2027-03-12 10:00:00', 'ends_at' => '2027-03-12 11:00:00']);

        $this->assertSame(['Primera', 'Segunda'], $event->sessions()->pluck('title')->all(),
            'T3-2: sessions() debe ser hasMany(EventSession::class) ordenada por starts_at.');
    }

    // ---------------------------------------------------------------- T3-3

    #[Group('t3-3')]
    public function test_t3_3_los_datos_correctos_pasan_la_validacion(): void
    {
        $this->assertFalse($this->falla([]), 'T3-3: unos datos correctos no deberían dar error.');
        $this->assertFalse($this->falla(['description' => null]), 'T3-3: description debe ser opcional (nullable).');
    }

    #[Group('t3-3')]
    public function test_t3_3_los_datos_incorrectos_se_rechazan(): void
    {
        $casos = [
            'title vacío' => ['title' => ''],
            'title de 121 caracteres' => ['title' => str_repeat('a', 121)],
            'starts_at que no es fecha' => ['starts_at' => 'mañana'],
            'ends_at anterior a starts_at' => ['ends_at' => '2027-03-12 09:00:00'],
            'room vacía' => ['room' => ''],
            'room de 81 caracteres' => ['room' => str_repeat('a', 81)],
            'capacity 0' => ['capacity' => 0],
            'capacity 5001' => ['capacity' => 5001],
            'capacity con decimales' => ['capacity' => 2.5],
        ];

        foreach ($casos as $caso => $cambio) {
            $this->assertTrue($this->falla($cambio), "T3-3: la validación debería rechazar: {$caso}.");
        }
    }

    // ---------------------------------------------------------------- T3-4

    #[Group('t3-4')]
    public function test_t3_4_puedo_abrir_el_programa_de_mi_evento(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['account_id' => $user->account_id]);

        $this->actingAs($user)->get("/events/{$event->id}/sessions")->assertOk();
    }

    #[Group('t3-4')]
    public function test_t3_4_el_evento_de_otra_cuenta_responde_404(): void
    {
        $user = User::factory()->create();
        $otherEvent = Event::factory()->create();

        $this->actingAs($user)->get("/events/{$otherEvent->id}/sessions")->assertNotFound();
    }

    // ---------------------------------------------------------------- T3-5

    #[Group('t3-5')]
    public function test_t3_5_la_ruta_post_existe_con_su_nombre(): void
    {
        $this->assertSame(
            url('/events/7/sessions'),
            route('events.sessions.store', 7),
            'T3-5: falta la ruta POST con nombre events.sessions.store en routes/web.php.'
        );
    }

    #[Group('t3-5')]
    public function test_t3_5_guardar_crea_la_fila_y_vuelve_al_listado(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['account_id' => $user->account_id]);

        $this->actingAs($user)
            ->post("/events/{$event->id}/sessions", $this->datosValidos())
            ->assertRedirect("/events/{$event->id}/sessions")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('event_sessions', [
            'event_id' => $event->id,
            'account_id' => $user->account_id,
            'title' => 'Introducción a React',
        ]);
    }

    // ---------------------------------------------------------------- utilidades

    /** Devuelve true si la validación de T3-3 rechaza los datos válidos con el cambio indicado. */
    private function falla(array $cambio): bool
    {
        $datos = array_merge($this->datosValidos(), $cambio);

        // Petición simulada con usuario y ruta, por si las reglas los necesitan (Tarea 4).
        $user = $this->usuario ??= User::factory()->create();
        $request = StoreEventSessionRequest::create('/events/1/sessions', 'POST', $datos);
        $request->setUserResolver(fn () => $user);
        $request->setRouteResolver(fn () => (new Route('POST', '/events/{eventId}/sessions', []))->bind($request));

        return Validator::make($datos, $request->rules())->fails();
    }

    private ?User $usuario = null;

    /** @return array<string, mixed> */
    private function datosValidos(): array
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
