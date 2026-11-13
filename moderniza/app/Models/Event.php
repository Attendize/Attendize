<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Evento de Attendize (tabla "events").
 *
 * Columnas que usa Moderniza: id, title, description, start_date,
 * end_date, is_live (publicado), account_id y deleted_at.
 * El resto de columnas sigue gestionándolas Attendize.
 */
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_live' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // TODO T3-2: relación "un evento tiene muchas sesiones" (hasMany EventSession), ordenadas por starts_at.
    public function sessions(): HasMany
    {
        // Escribe aquí la relación.
    }

    /**
     * Eventos de la cuenta indicada. Es la base de la autorización:
     * cualquier consulta de gestión debe pasar por aquí.
     */
    public function scopeOfAccount(Builder $query, int $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }
}
