<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\EventViewController
 */
class EventViewControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function post_contact_organiser_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $event = factory(\App\Models\Event::class)->create();

        $response = $this->post(route('postContactOrganiser', ['event_id' => $event_id]), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function post_show_hidden_tickets_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $event = factory(\App\Models\Event::class)->create();

        $response = $this->post(route('postShowHiddenTickets', ['event_id' => $event_id]), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_calendar_ics_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $event = factory(\App\Models\Event::class)->create();

        $response = $this->get(route('downloadCalendarIcs', ['event_id' => $event_id]));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_event_home_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $event = factory(\App\Models\Event::class)->create();

        $response = $this->get(route('showEventPage', ['event_id' => $event_id]));

        $response->assertOk();
        $response->assertViewIs('Public.ViewEvent.EventNotLivePage');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_event_home_preview_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get(route('showEventPagePreview', ['event_id' => $event_id]));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
