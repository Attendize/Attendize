<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Usuario de Attendize (tabla "users").
 *
 * Moderniza reutiliza la tabla de Attendize: mismas cuentas, mismas
 * contraseñas (bcrypt). Por eso NO hay migración de "users" en Moderniza.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'users';

    /**
     * Campos asignables en masa. "account_id" no está aquí a propósito:
     * la cuenta nunca debe llegar desde el navegador.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /** Nombre completo para mostrar en la interfaz. */
    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
