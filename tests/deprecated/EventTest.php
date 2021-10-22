<?php

use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EventTest extends TestCase
{
    public function test_event_is_created_successfully()
    {
        $this->actingAs($this->test_user);

        $organiser = factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

        $post = [
            'organiser_id' => $organiser->id,
            'title' => $this->faker->text,
            'description' => $this->faker->paragraph,
            'location_venue_name' => $this->faker->company,
            'location_address_line_1' => $this->faker->streetAddress,
            'location_address_line_2' => '',
            'location_state' => $this->faker->city,
            'location_post_code' => $this->faker->postcode,
            'start_date' => date('d-m-Y H:i', strtotime('+ 30 days')),
            'end_date' => date('d-m-Y H:i', strtotime('+ 60 days')),
        ];

        $this->call('post', route('postCreateEvent'), $post, $server);

        $this->seeJson([
            'status' => 'success',
            'id' => 1,
        ]);
    }

    public function test_event_is_not_created_and_validation_error_messages_show()
    {
        $this->actingAs($this->test_user);

        $organiser = factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];

        $post = [
            'organiser_id' => $organiser->id,
        ];

        $this->call('post', route('postCreateEvent'), $post, $server);

        $this->seeJson([
            'status' => 'error',
        ]);
    }

    public function test_event_can_be_edited()
    {
        $organiser = factory(App\Models\Organiser::class)->create(['account_id' => 1]);
        $event = factory(App\Models\Event::class)->create([
            'account_id'   => $organiser->account_id,
            'organiser_id' => $organiser->id,
            'user_id'      => $this->test_user->id,
        ]);

        $this->actingAs($this->test_user)
            ->visit(route('showEventCustomize', ['event_id' => $event->id]))
            ->type($this->faker->text, 'title')
            ->type($this->faker->paragraph, 'description')
            ->press('Save Changes')
            ->seeJson([
                'status' => 'success',
            ]);
    }
}
