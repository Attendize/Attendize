<?php

namespace Tests\Unit\PDFGenerator;

use App\Generators\TicketGenerator;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TicketGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test demo data generator
     *
     * @return void
     */
    public function testDemoData()
    {
        // Creates an event
        $event = factory(Event::class)->create();

        // Create a demo ticket for this event
        $order = TicketGenerator::demoData($event->id);

        // Return data is an order
        $this->assertInstanceOf(Order::class, $order);

        // Check necessary data for generate tickets
        $this->assertIsInt($order->id);
        $this->assertNotEmpty($order->order_reference);

        // Demo Order Has Event
        $this->assertInstanceOf(Event::class, $order->event);

        // Demo Order Has Attendees
        foreach ($order->attendees as $attendee) {
            $this->assertInstanceOf(Attendee::class, $attendee);

            // Demo Attendee Has Ticket
            $this->assertInstanceOf(Ticket::class, $attendee->ticket);

            // Check necessary data for generate tickets
            $this->assertInstanceOf(Order::class, $attendee->order);
            $this->assertNotEmpty($attendee->private_reference_number);
            $this->assertNotEmpty($attendee->reference);
            $this->assertNotEmpty($attendee->first_name);
            $this->assertNotEmpty($attendee->last_name);

            $this->assertInstanceOf(Event::class, $attendee->ticket->event);
            $this->assertNotEmpty($attendee->ticket->title);
            $this->assertIsNumeric($attendee->ticket->price);
        }
    }

    /**
     * Test filename generation for order
     */
    public function testGenerateFileNameForOrder()
    {
        $order = factory(Order::class)->make();

        $pdf_file = TicketGenerator::generateFileName($order);

        // Ticket name is equal to order_reference
        $this->assertSame($order->order_reference, $pdf_file->name);

        // Folder is same as config
        $this->assertSame($pdf_file->base_path, public_path(config('attendize.event_pdf_tickets_path')));

        // It is not an attendee ticket, so it should not contain '-' in the name
        $this->assertStringNotContainsString('-', $pdf_file->name);
    }

    /**
     * Test filename generation for order
     */
    public function testGenerateFileNameForAttendee()
    {
        $attendee = factory(Attendee::class)->make();

        $pdf_file = TicketGenerator::generateFileName($attendee->order, $attendee);

        // Ticket name is equal to order_reference plus attendee reference index
        $this->assertSame(
            $attendee->order->order_reference . '-' . $attendee->reference_index,
            $pdf_file->name);

        // Folder is same as config
        $this->assertSame($pdf_file->base_path, public_path(config('attendize.event_pdf_tickets_path')));

        // It's an attendee ticket, so it should contain '-' in the name
        $this->assertStringContainsString('-', $pdf_file->name);
    }

    /**
     * Check if giver variable is attendee or not
     */
    public function testIsAttendeeTicket()
    {
        // Is Attendee
        $this->assertTrue(TicketGenerator::isAttendeeTicket(factory(Attendee::class)->make()));

        // Is not Attendee
        $this->assertFalse(TicketGenerator::isAttendeeTicket(factory(Order::class)->make()));

        // Is not Attendee
        $this->assertFalse(TicketGenerator::isAttendeeTicket(null));
    }


    /**
     * Test PDF Ticket generation for orders
     */
    public function testCreatePDFTicketForOrder(): void
    {
        // We need an Attendee for having an order with attendees
        $attendee = factory(Attendee::class)->make();

        $pdf_file = TicketGenerator::createPDFTicket($attendee->order);

        // File exist
        $this->assertFileExists($pdf_file->path);

        // File is a valid PDF file
        $this->assertSame(
            finfo_file(finfo_open(FILEINFO_MIME_TYPE), $pdf_file->path),
            'application/pdf'
        );

        // Verify that the PDF is not generated again if it has already been generated
        $pdf_file2 = TicketGenerator::createPDFTicket($attendee->order);

        // PDF is cached
        $this->assertTrue($pdf_file2->cached);

        // File exist
        $this->assertFileExists($pdf_file->path);

        // File is a valid PDF file
        $this->assertSame(
            finfo_file(finfo_open(FILEINFO_MIME_TYPE), $pdf_file->path),
            'application/pdf'
        );

        // Delete test file
        $this->assertTrue(File::delete($pdf_file->path));
    }

    /**
     * Test PDF Ticket generation for attendees
     */
    public function testCreatePDFTicketForAttendee(): void
    {
        $attendee = factory(Attendee::class)->make();

        $pdf_file = TicketGenerator::createPDFTicket($attendee->order, $attendee);

        // File is a valid PDF file
        $this->assertSame(
            finfo_file(finfo_open(FILEINFO_MIME_TYPE), $pdf_file->path),
            'application/pdf'
        );

        // Verify that the PDF is not generated again if it has already been generated
        $pdf_file2 = TicketGenerator::createPDFTicket($attendee->order, $attendee);

        // PDF is cached
        $this->assertTrue($pdf_file2->cached);

        // File exist
        $this->assertFileExists($pdf_file->path);

        // File is a valid PDF file
        $this->assertSame(
            finfo_file(finfo_open(FILEINFO_MIME_TYPE), $pdf_file->path),
            'application/pdf'
        );

        // Delete test file
        $this->assertTrue(File::delete($pdf_file->path));
    }
}
