<?php

namespace App\Http\Resources;

use App\Models\EventSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Representación PÚBLICA de una sesión en la API REST.
 * Solo se publican los datos que necesita el programa (AC6):
 * nunca account_id, event_id interno, is_published ni marcas de tiempo.
 *
 * @mixin EventSession
 */
class PublicSessionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        // TODO T4-3: devuelve id, title, description, starts_at y ends_at (formato ISO 8601,
        // con ->toIso8601String()), room y capacity. NADA más.
        return [];
    }
}
