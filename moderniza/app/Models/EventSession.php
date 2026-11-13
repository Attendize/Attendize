<?php

namespace App\Models;

use Database\Factories\EventSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sesión del programa de un evento (tabla nueva "event_sessions").
 * Es la entidad del núcleo modernizado de TechFest.
 */
class EventSession extends Model
{
    /** @use HasFactory<EventSessionFactory> */
    use HasFactory;

    protected $table = 'event_sessions';

    // TODO T3-2: indica qué campos se pueden asignar en masa.
    // Pista: solo los que escribe el organizador en el formulario.
    // ¿Debe estar account_id o event_id? Justifícalo en tu Pull Request.
    protected $fillable = [];

    protected function casts(): array
    {
        // TODO T3-2: convierte starts_at y ends_at a fecha y capacity a entero.
        return [];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
