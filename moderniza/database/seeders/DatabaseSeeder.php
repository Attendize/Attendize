<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Moderniza NO siembra datos en la base "attendize".
 * Los organizadores y eventos se crean desde Attendize (Tarea 1);
 * las pruebas automáticas crean sus propios datos con factorías.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('Moderniza no siembra datos: usa los datos creados en Attendize.');
    }
}
