<?php


namespace App\Generators;


use App\Models\Attendee;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\PDFGenerator\PDFFile;
use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\Exception;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

/**
 * Create a ticket using Intervention Image
 *
 * Class TicketGenerator
 * @package App\Generators
 */
class TicketGenerator
{

    /**
     * Generate a fake ticket data for demo purposes
     *
     * @param  int  $event_id  Event ID
     * @return Order
     */
    public static function demoData(int $event_id)
    {
        $order = new Order();
        $order->id = 1;
        $order->order_reference = trans('Ticket.demo_order_ref');

        // Event data
        $order->event = Event::findOrFail($event_id);

        // Atendee data
        $attendee = new Attendee();
        $attendee->order = $order;
        $attendee->private_reference_number = 'hello';
        $attendee->reference = trans('Ticket.demo_attendee_ref');
        $attendee->first_name = trans('Ticket.demo_first_name');
        $attendee->last_name = trans('Ticket.demo_last_name');

        $attendee->ticket = new Ticket();
        $attendee->ticket->event = $order->event;
        $attendee->ticket->title = trans('Ticket.demo_ticket_type');
        $attendee->ticket->price = trans('Ticket.demo_price');

        $order->attendees = [
            $attendee
        ];

        return $order;
    }

    /**
     * Create and save a PDF Ticket
     *
     * @param  Order  $order
     * @param  Attendee|null  $attendee
     * @param  Bool|false  $ignoreDiskCache
     * @return PDFFile
     */
    public static function createPDFTicket(Order $order, Attendee $attendee = null, $ignoreDiskCache = false)
    {
        Log::info('Generating ticket for order: #' . $order->id . '. Reference ' . $order->order_reference);

        // Generate file name
        $pdf_file = self::generateFileName($order, $attendee);

        // Check if file exist before create it again
        if (file_exists($pdf_file->path) && $ignoreDiskCache == false) {
            Log::debug('Use ticket from cache: ' . $pdf_file->path);

            $pdf_file->cached = true;
            return $pdf_file;
        }

        // Data for view
        $data = [
            'banner' => self::createBanner($order),
            'order' => $order,
        ];

        try {
            PDF::loadView('Public.ViewEvent.Partials.PDFTicket', $data)->save($pdf_file->path);
            Log::debug('Ticket generated for order ' . $order->id . '. PDF file: ' . $pdf_file->path);
        } catch (Exception $exception) {
            Log::error('Error generating ticket for order ' . $order->id);
            Log::error('Error message. ' . $exception->getMessage());
            Log::error('Error stack trace' . $exception->getTraceAsString());
            $pdf_file->error = true;
        }

        return $pdf_file;
    }

    /**
     * Generate filename and path for generated PDFs
     *
     * @param  Order  $order  Order data
     * @param  Attendee|null  $attendee  Attendee index
     * @return mixed
     */
    public static function generateFileName(Order $order, Attendee $attendee = null)
    {
        if (self::isAttendeeTicket($attendee)) {
            $name = implode('-', [$order->order_reference, $attendee->reference_index]);
        } else {
            $name = $order->order_reference;
        }

        return new PDFFile($name);
    }

    /**
     * Verify whether a ticket should be generated for only one attendee.
     *
     * @param  Attendee|null  $attendee
     * @return bool
     */
    public static function isAttendeeTicket($attendee): bool
    {
        return ($attendee !== null && $attendee instanceof Attendee);
    }

    /**
     * Create the banner/flyer image for the ticket if it hasn't already been created.
     *
     * @return \Intervention\Image\Image
     */
    private static function createBanner($order)
    {
        // Get the event flyer
        $flyer = optional($order->event->images->first())->image_path;

        // If no flyer use default flyer
        if ($flyer === null || !file_exists(public_path($flyer))) {
            $flyer = config('attendize.ticket.image.default');
        }

        $image_path = public_path($flyer.'-1360.jpg');
        if (!file_exists($image_path)) {
            Image::make(public_path($flyer))->fit(1360, 635)->save($image_path);
        }
        return $image_path;
    }
}
