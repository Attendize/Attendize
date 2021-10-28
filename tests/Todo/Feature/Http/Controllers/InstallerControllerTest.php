<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\InstallerController
 */
class InstallerControllerTest extends TestCase
{
    /**
     * @test
     */
    public function post_installer_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->post(route('postInstaller'), [
            // TODO: send request data
        ]);

        $response->assertOk();
        $response->assertViewIs('Installer.Installer');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_installer_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get(route('showInstaller'));

        $response->assertOk();
        $response->assertViewIs('Installer.AlreadyInstalled');

        // TODO: perform additional assertions
    }

    // test cases...
}
