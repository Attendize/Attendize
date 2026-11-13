<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Las pruebas no necesitan los archivos compilados de React (Vite):
        // así funcionan aunque el contenedor moderniza-vite esté parado.
        $this->withoutVite();
    }
}
