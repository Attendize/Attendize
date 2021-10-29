<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\OrganiserViewController
 */
class OrganiserViewControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function show_organiser_home_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $organiser = factory(\App\Models\Organiser::class)->create();

        $response = $this->get(route('showOrganiserHome', ['organiser_id' => $organiser_id]));

        $response->assertOk();
        $response->assertViewIs('Public.ViewOrganiser.OrganiserPage');

        // TODO: perform additional assertions
    }

    // test cases...
}
