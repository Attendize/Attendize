<?php

namespace App\Rules;

use App\Models\EventSession;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * CAMBIO DE REQUISITO · Tarea 4
 *
 * "Dos sesiones del mismo evento no pueden coincidir en la misma sala
 *  en horarios que se solapen."
 *
 * Dos intervalos [A_inicio, A_fin) y [B_inicio, B_fin) se solapan si:
 *     A_inicio < B_fin  Y  A_fin > B_inicio
 * Una sesión que empieza justo cuando termina otra NO se solapa.
 *
 * Se aplica al campo "room" y usa también starts_at y ends_at del formulario.
 */
class NoRoomOverlap implements DataAwareRule, ValidationRule
{
    /** @var array<string, mixed> */
    protected array $data = [];

    /**
     * @param  int  $eventId  evento de la ruta
     * @param  int  $accountId  cuenta del usuario autenticado: solo se consultan
     *                          sesiones de SU cuenta (no se revela nada de otras)
     * @param  int|null  $ignoreSessionId  sesión que se está editando (si la hay)
     */
    public function __construct(
        private int $eventId,
        private int $accountId,
        private ?int $ignoreSessionId = null,
    ) {}

    /** Laravel entrega aquí todos los datos del formulario. */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Si las fechas faltan o no son válidas, ya fallan sus propias reglas.
        try {
            $start = Carbon::parse((string) ($this->data['starts_at'] ?? ''));
            $end = Carbon::parse((string) ($this->data['ends_at'] ?? ''));
        } catch (Throwable) {
            return;
        }
        if (empty($this->data['starts_at']) || empty($this->data['ends_at']) || $end->lessThanOrEqualTo($start)) {
            return;
        }

        // TODO T4-1: comprueba si ya existe una sesión que se solape en la misma sala.
        // Busca en EventSession: mismo event_id, misma account_id, misma room ($value),
        // starts_at < $end  Y  ends_at > $start  (y que no sea $this->ignoreSessionId).
        // Si existe, llama a $fail('La sala ... ya está ocupada en ese horario.').
    }
}
