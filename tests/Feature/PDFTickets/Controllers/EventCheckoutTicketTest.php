<?php

namespace Tests\Feature;

use App\Generators\TicketGenerator;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\File as FileResponse;
use Tests\TestCase;

class EventCheckoutTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_demo_ticket_can_be_viewed()
    {
        $event = factory(Event::class)->create();

        $this->get(route('showOrderTickets', ['order_reference' => 'example', 'event' => $event->id]))
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf'); // Example ticket is returned in PDF

    }

    /** @test */
    public function a_ticket_can_be_viewed()
    {
        $this->withOrganiser();

        $attendee = factory(Attendee::class)->create(['account_id' => $this->organiser->account_id]);

        $response = $this->get(
            route('showOrderTickets', ['order_reference' => $attendee->order->order_reference])
        );

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $pdf_file = TicketGenerator::generateFileName($attendee->order);

        $this->assertTrue(File::delete($pdf_file->path));
    }

    /** @test */
    public function a_ticket_can_be_downloaded()
    {
        $this->withoutExceptionHandling();

        $this->withOrganiser();

        $attendee = factory(Attendee::class)->create(['account_id' => $this->organiser->account_id]);

        $response = $this->get(
            route(
                'showOrderTickets',
                [
                    'order_reference' => $attendee->order->order_reference,
                    'download'        => 1
                ]
            )
        );

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $pdf_file = TicketGenerator::generateFileName($attendee->order);

        $response->assertHeader(
            'content-disposition',
            'attachment; filename=' . $pdf_file->basename
        );

        /** @var FileResponse $file */
        $this->assertFileExists($response->baseResponse->getFile()->getPath());

        $this->assertTrue(File::delete($pdf_file->path));
    }

}
