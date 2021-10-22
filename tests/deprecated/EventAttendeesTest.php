<?php

use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EventAttendeesTest extends TestCase
{
    public function test_event_attendees_are_displayed()
    {
        // Create organiser with account id = 1 to skip first run
        $organiser = factory(App\Models\Organiser::class)->create([
            'account_id' => 1,
        ]);

        $event = factory(App\Models\Event::class)->create([
            'account_id' => $organiser->account_id,
        ]);

        $attendee = factory(App\Models\Attendee::class)->create([
            'account_id' => $organiser->account_id,
            'event_id' => $event->id,
            'first_name' => 'Test First Name',
            'last_name' => 'Test Last Name',
        ]);

        $this->actingAs($this->test_user)
            ->visit(route('showEventAttendees', ['event_id' => $attendee->event->id]))
            ->see('Attendees')
            ->see('Test First Name')
            ->see('Test Last Name');
    }
}
