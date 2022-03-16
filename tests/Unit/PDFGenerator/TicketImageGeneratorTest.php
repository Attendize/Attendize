<?php

namespace Tests\Unit\PDFGenerator;

use App\Generators\TicketImageGenerator;
use App\Models\Attendee;
use App\Models\EventImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
use Tests\TestCase;

class TicketImageGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test PDF Ticket generation for all attendees
     */
    public function testCreateImageTickets(): void
    {
        $attendee = factory(Attendee::class)->make();

        $ticket_image_generator = new TicketImageGenerator($attendee->order);

        $images = $ticket_image_generator->createImageTickets($attendee);

        $this->assertIsArray($images);

        foreach ($images as $image) {
            $this->assertInstanceOf(Image::class, $image);
        }
    }

    /**
     * Test PDF Ticket generation for an attendee
     */
    public function testCreateImageTicketWithoutImages(): void
    {
        $attendee = factory(Attendee::class)->make();

        $ticket_image_generator = new TicketImageGenerator($attendee->order);

        $image = $ticket_image_generator->createImageTicket($attendee);

        $this->assertInstanceOf(Image::class, $image);
    }

    /**
     * Test PDF Ticket generation for an attendee
     */
    public function testCreateImageTicketWithImages(): void
    {
        Storage::fake('tickets');

        $attendee_with_image = factory(Attendee::class)->make();

        $event_image = factory(EventImage::class, 2)->make([
            'event_id' => $attendee_with_image->event->id
        ]);

        $attendee_with_image->images = $event_image;

        $ticket_image_generator = new TicketImageGenerator($attendee_with_image->order);

        $image = $ticket_image_generator->createImageTicket($attendee_with_image);

        $this->assertInstanceOf(Image::class, $image);

        foreach ($event_image as $image_to_delete) // Delete test files
        {
            $this->assertTrue(File::delete($image_to_delete->image_path));
        }
    }


}
