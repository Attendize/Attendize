<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Organiser;
use Tests\TestCase;

class OrganiserEventsTest extends TestCase
{
    public function test_show_events_displays_events()
    {
        $organiser = factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $event1 = factory(App\Models\Event::class)->create([
            'account_id'   => $organiser->account_id,
            'organiser_id' => $organiser->id,
            'user_id'      => $this->test_user->id,
        ]);

        $event2 = factory(App\Models\Event::class)->create([
            'account_id'   => $organiser->account_id,
            'organiser_id' => $organiser->id,
            'user_id'      => $this->test_user->id,
        ]);

        $this->actingAs($this->test_user)
            ->get(route('showOrganiserEvents', ['organiser_id' => $organiser->id]))
            ->assertSee($event1->title)
            ->assertSee($event2->title)
            ->assertSee('2 events');
    }
}