<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;

/**
 * Base de datos para las pruebas (proporcionado por el profesorado).
 *
 * - Usa RefreshDatabase: BORRA y recrea todas las tablas de la base de pruebas.
 * - Por eso se negará a ejecutarse si la base no termina en "_test".
 * - Crea primero las tablas mínimas de Attendize y después las de Moderniza.
 */
trait UsesTestDatabase
{
    use RefreshDatabase;

    protected function migrateFreshUsing(): array
    {
        $connection = config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        if (! str_ends_with($database, '_test')) {
            throw new RuntimeException(
                "Pruebas detenidas: la base \"{$database}\" no es de pruebas. ".
                'Nunca ejecutes las pruebas contra la base "attendize".'
            );
        }

        return [
            '--path' => ['database/testing/attendize', 'database/migrations'],
        ];
    }
}
