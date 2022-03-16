<?php

namespace Tests\Feature;

use App\Generators\TicketGenerator;
use App\Models\Attendee;
use App\Models\Organiser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\File as FileResponse;
use Tests\TestCase;

class EventAttendeeTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_download_attendee_tickets()
    {
        $attendee = factory(Attendee::class)->create();

        $this->get(
            route('showExportTicket', ['event_id' => $attendee->event_id, 'attendee_id' => $attendee->id])
        )
            ->assertStatus(302)
            ->assertLocation(route('login'));

    }

    /** @test */
    public function an_organiser_cannot_download_another_organiser_attendee_tickets()
    {
        $this->signIn()->withOrganiser(['account_id' => Auth::user()->account_id]);

        $another_organiser = factory(Organiser::class)->create();

        $attendee = factory(Attendee::class)->create(['account_id' => $another_organiser->account_id]);

        $this->get(
            route('showExportTicket', ['event_id' => $attendee->event_id, 'attendee_id' => $attendee->id])
        )
            ->assertStatus(404);
        // TODO: This should return an Unauthorized Code
    }

    /** @test */
    public function an_organiser_can_download_tickets_from_his_attendees()
    {
        $this->signIn()->withOrganiser(['account_id' => Auth::user()->account_id]);

        $attendee = factory(Attendee::class)->create(['account_id' => $this->organiser->account_id]);

        $response = $this->get(
            route('showExportTicket', ['event_id' => $attendee->event_id, 'attendee_id' => $attendee->id])
        );

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $pdf_file = TicketGenerator::generateFileName($attendee->order, $attendee);
        $response->assertHeader(
            'content-disposition',
            'attachment; filename=' . $pdf_file->basename
        );

        /** @var FileResponse $file */
        $this->assertFileExists($response->baseResponse->getFile()->getPath());

        $this->assertTrue(File::delete($pdf_file->path));
    }


    /** @test */
    public function guests_cannot_view_attendee_tickets()
    {
        $attendee = factory(Attendee::class)->create();

        $this->get(
            route('showAttendeeTicket', ['event_id' => $attendee->event_id, 'attendee_id' => $attendee->id])
        )
            ->assertStatus(302)
            ->assertLocation(route('login'));

    }

    /** @test */
    public function an_organiser_cannot_view_another_organiser_attendee_tickets()
    {
        $this->signIn()->withOrganiser(['account_id' => Auth::user()->account_id]);

        $another_organiser = factory(Organiser::class)->create();

        $attendee = factory(Attendee::class)->create(['account_id' => $another_organiser->account_id]);

        $this->get(
            route('showAttendeeTicket', ['event_id' => $attendee->event_id, 'attendee_id' => $attendee->id])
        )
            ->assertStatus(404);
        // TODO: This should return an Unauthorized Code
    }

    /** @test */
    public function an_organiser_can_view_and_download_tickets_from_his_attendees()
    {
        $this->signIn()->withOrganiser(['account_id' => Auth::user()->account_id]);

        $attendee = factory(Attendee::class)->create(['account_id' => $this->organiser->account_id]);

        // View File
        $view_file_response = $this->get(
            route('showAttendeeTicket', ['event_id' => $attendee->event_id, 'attendee_id' => $attendee->id])
        );

        $view_file_response->assertStatus(200);
        $view_file_response->assertHeader('content-type', 'application/pdf');

        // Download File
        $download_response = $this->get(
            route(
                'showAttendeeTicket',
                [
                    'event_id'    => $attendee->event_id,
                    'attendee_id' => $attendee->id,
                    'download'    => 1
                ]
            )
        );

        $download_response->assertStatus(200);
        $download_response->assertHeader('content-type', 'application/pdf');

        $pdf_file = TicketGenerator::generateFileName($attendee->order, $attendee);
        $download_response->assertHeader(
            'content-disposition',
            'attachment; filename=' . $pdf_file->basename
        );

        /** @var FileResponse $file */
        $this->assertFileExists($download_response->baseResponse->getFile()->getPath());

        $this->assertTrue(File::delete($pdf_file->path));
    }
}
