<?php


namespace App\Generators;


use App\Models\Attendee;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Intervention\Image\Facades\Image;
use Intervention\Image\Gd\Font;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

/**
 * Create a ticket using Intervention Image
 *
 * Class TicketGenerator
 * @package App\Generators
 */
class TicketGenerator
{
    /**
     * Order information
     *
     * @var Order $order
     */
    private $order;

    /**
     * Array where store all generated tickets
     *
     * @var array $all_tickets
     */
    private $all_tickets = [];

    /**
     * Ticket Banner/Flyer
     *
     * @var mixed $ticket_banner
     */
    private $ticket_banner = null;

    /**
     * Organizer Logo
     *
     * @var mixed $organizer_logo
     */
    private $organizer_logo = null;

    /**
     * Ticket Generator constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Create all tickets for an order
     *
     * @return array
     */
    public function createTickets()
    {
        foreach ($this->order->attendees as $attendee) {
            // Only generate if not cancelled
            if (!$attendee->is_cancelled) {
                $this->all_tickets[] = $this->createTicket($attendee);
            }
        }

        return $this->all_tickets;
    }


    /**
     * Create a ticket
     *
     * @param Attendee $attendee
     * @return mixed
     */
    public function createTicket(Attendee $attendee)
    {
        // Create the ticket canvas
        $ticket = Image::canvas(1725, 675, $this->order->event->ticket_bg_color);

        // Add banner image to the ticket
        $ticket->insert($this->createBanner(), 'top-right', 20, 20);

        // Add organizer logo to the ticket
        $ticket->insert($this->createOrganizerLogo(), 'top-left', 20, 20);

        // Add QR image to the ticket
        $ticket->insert($this->createQRCode($attendee), 'top-left', 32, 100);

        if ($this->order->event->is_1d_barcode_enabled) {
            // Add barcode image to the ticket
            $ticket->insert($this->create1DBarcode($attendee), 'bottom-left', 20, 20);
        }

        // Add Reference Number
        $ticket = $this->createReferenceNumber($ticket, $attendee);

        // Create organizer name
        $ticket = $this->createOrganizerName($ticket);

        // Add event title
        $ticket = $this->createEventTitle($ticket);

        // Add info text
        $ticket = $this->createInfoText($ticket, $attendee);

        // Add price text
        $ticket = $this->createPrice($ticket, $attendee);

        return $ticket;
    }

    /**
     * Create the banner/flyer image for the ticket if it hasn't already been created.
     *
     * @return \Intervention\Image\Image
     */
    private function createBanner()
    {
        // This prevents image recreation and saves server resources.
        if (!$this->ticket_banner === null) {
            return $this->ticket_banner;
        }

        // Create banner Image (Recommended Size 1360x635) and store it in the "cache"
        $this->ticket_banner = Image::make(public_path($this->order->event->images->first()->image_path))
            ->resize(1360, null)
            ->crop(1360, 635, 0, 0);

        return $this->ticket_banner;
    }

    /**
     * Create the Organizer Logo image for the ticket if it hasn't already been created.
     *
     * @return \Intervention\Image\Image
     */
    private function createOrganizerLogo()
    {
        // This prevents image recreation and saves server resources.
        if (!$this->organizer_logo === null) {
            return $this->organizer_logo;
        }

        // Create Organizer Logo (Recommended Size 304x81) and store it in the "cache"
        $this->organizer_logo = Image::make(public_path($this->order->event->organiser->full_logo_path))
            ->widen(304);

        return $this->organizer_logo;
    }

    /**
     * Create QR container and Barcode
     *
     * @param Attendee $attendee
     * @return \Intervention\Image\Image
     */
    private function createQRCode(Attendee $attendee)
    {
        // Create QR container
        $qrcontainer = Image::canvas(280, 280, '#ffffff');

        // Create the QR (Recommended Size 240x240)
        $qrcode = Image::make(
            DNS2D::getBarcodePNG($attendee->private_reference_number, "QRCODE", 240, 240)
        )->resize(240, 240);

        // Add QR image to the container
        $qrcontainer->insert($qrcode, 'center', 0, 0);

        return $qrcontainer;
    }

    /**
     * Create 1D container and Barcode
     *
     * @param Attendee $attendee
     * @return \Intervention\Image\Image
     */
    private function create1DBarcode(Attendee $attendee)
    {
        // Create the barcode container (Recommended Size 304x80)
        $barcodeContainer = Image::canvas(304, 80, '#ffffff');

        // Create the barcode (Recommended Size 280x60)
        $barcode = Image::make(
            DNS1D::getBarcodePNG($attendee->private_reference_number, "C39+", 100, 200)
        )->resize(280, 60);

        $barcodeContainer->insert($barcode, 'center', 0, 0);

        return $barcodeContainer;
    }

    /**
     * Create Reference Number text
     *
     * @param \Intervention\Image\Image $ticket
     * @param Attendee $attendee
     * @return \Intervention\Image\Image
     */
    private function createReferenceNumber(\Intervention\Image\Image $ticket, Attendee $attendee)
    {
        // $order->order_reference
        return $ticket->text($attendee->reference, 171, 400, function ($font) {
            /** @var Font $font */
            $font->file(public_path('assets/fonts/OpenSans-Bold.ttf'));
            $font->size(42);
            $font->color($this->order->event->ticket_text_color);
            $font->align('center');
            $font->valign('top');
        });
    }

    /**
     * Create Organizer Name text
     *
     * @param \Intervention\Image\Image $ticket
     * @return \Intervention\Image\Image
     */
    private function createOrganizerName(\Intervention\Image\Image $ticket)
    {
        // Add organiser name
        return $ticket->text($this->shortenText($this->order->event->organiser->name), 171, 480,
            function ($font) {
                /** @var Font $font */
                $font->file(public_path('assets/fonts/OpenSans-Regular.ttf'));
                $font->size(22);
                $font->color($this->order->event->ticket_text_color);
                $font->align('center');
                $font->valign('top');
            }
        );
    }

    /**
     * Create Event title text
     *
     * @param \Intervention\Image\Image $ticket
     * @return \Intervention\Image\Image
     */
    private function createEventTitle(\Intervention\Image\Image $ticket)
    {
        // Add organiser name
        return $ticket->text($this->shortenText($this->order->event->title), 171, 520,
            function ($font) {
                /** @var Font $font */
                $font->file(public_path('assets/fonts/OpenSans-Regular.ttf'));
                $font->size(22);
                $font->color($this->order->event->ticket_text_color);
                $font->align('center');
                $font->valign('top');
            }
        );
    }

    /**
     * Create Info Text text
     *
     * @param \Intervention\Image\Image $ticket
     * @param Attendee $attendee
     * @return \Intervention\Image\Image
     */
    private function createInfoText(\Intervention\Image\Image $ticket, Attendee $attendee)
    {
        // Add text lines
        $lines = [
            // Line 1
            [
                'y'    => 28,
                'text' => [
                    $this->shortenText($attendee->first_name . ' ' . $attendee->last_name, 52),
                    $this->shortenText($attendee->ticket->title, 52)
                ]
            ],
            // Line 2
            [
                'y'    => 58,
                'text' => [
                    $this->shortenText($this->order->event->venue_name, 52),
                    $this->order->event->startDateFormatted(),
                    $this->order->event->endDateFormatted()
                ]
            ]
        ];

        // Create text container
        $text_container = Image::canvas(1319, 80, $this->order->event->ticket_bg_color)->opacity(80);

        // Insert each line
        foreach ($lines as $line) {
            $text_container->text(join(' · ', $line['text']), 20, $line['y'], function ($font) {
                /** @var Font $font */
                $font->file(public_path('assets/fonts/OpenSans-SemiBold.ttf'));
                $font->size(22);
                $font->color($this->order->event->ticket_text_color);
                $font->align('left');
                $font->valign('middle');
            });
        }

        // Add text canvas to the ticket
        return $ticket->insert($text_container, 'bottom-right', 40, 40);
    }

    /**
     * Create Price text
     *
     * @param \Intervention\Image\Image $ticket
     * @param Attendee $attendee
     * @return \Intervention\Image\Image
     */
    private function createPrice(\Intervention\Image\Image $ticket, Attendee $attendee)
    {
        // Price calculation
        // Calculating grand total including tax
        $grand_total = $attendee->ticket->total_price;
        $tax_amt = ($grand_total * $this->order->event->organiser->tax_value) / 100;
        $grand_total = $tax_amt + $grand_total;

        $money = money($grand_total, $this->order->event->currency);

        $canvas_width = strlen($money) * 20;

        // Create text container
        $price_container = Image::canvas($canvas_width, 60, $this->order->event->ticket_bg_color)->opacity(80);

        $price_container->text($money, $canvas_width / 2, 30, function ($font) {
            /** @var Font $font */
            $font->file(public_path('assets/fonts/OpenSans-Bold.ttf'));
            $font->size(40);
            $font->color($this->order->event->ticket_text_color);
            $font->align('center');
            $font->valign('middle');
        });

        // Add QR image to the ticket
        return $ticket->insert($price_container, 'top-right', 40, 40);
    }

    /**
     * Shortens the text if it is too long
     *
     * @param $text
     * @param int $limit
     * @return string
     */
    private function shortenText($text, $limit = 20)
    {
        if (strlen($text) > $limit) {
            return substr($text, 0, $limit) . '...';
        }

        return $text;
    }

    /**
     * Generate a fake ticket for demo purposes
     *
     * @param int $event_id Event ID
     * @return Order
     */
    public static function demoData(int $event_id)
    {
        $order = new Order();
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
     * Generate filename and path for generated PDFs
     *
     * @param $reference
     * @return mixed
     */
    public static function generateFileName($reference)
    {
        $file['name'] = $reference;
        $file['base_name'] = $file['name'] . '.pdf';
        $file['base_path'] = public_path(config('attendize.event_pdf_tickets_path')) . '/';
        $file['file_path'] = $file['base_path'] . $file['name'];
        $file['fullpath'] = $file['file_path'] . $file['base_name'];

        return $file;
    }
}