<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use App\Generators\TicketGenerator;
use App\Models\Order;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Generate Ticket as a Job
 *
 * @package App\Jobs
 */
class GenerateTicket extends Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    

    /**
     * @var string $reference Full reference id
     */
    protected $reference;

    /**
     * @var string $order_reference Reference ID
     */
    protected $order_reference;

    /**
     * @var int|null $attendee_reference_index Attendee Index
     */
    protected $attendee_reference_index;

    /**
     * Create a new job instance.
     *
     * @param string $reference
     * @return void
     */
    public function __construct($reference)
    {
        Log::info("Generating ticket: #" . $reference);

        $this->reference = $reference;

        // Assign order reference
        $this->order_reference = explode("-", $reference)[0];

        // If reference index isset assign it
        if (strpos($reference, "-")) {
            $this->attendee_reference_index = explode("-", $reference)[1];
        }
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Generate file name
        $pdf_file = TicketGenerator::generateFileName($this->reference);

        // Check if file exist before create it again
        if (file_exists($pdf_file['fullpath'])) {
            Log::info('Use ticket from cache: ' . $pdf_file['fullpath']);
            return;
        }

        // Find the order
        /** @var Order $order */
        $order = Order::where('order_reference', $this->order_reference)->first();

        Log::info($order);

        // If only need a single attendee find it
        $query = $order->attendees();
        if ($this->isAttendeeTicket()) {
            $query = $query->where('reference_index', '=', $this->attendee_reference_index);
        }
        $order->attendees = $query->get();

        // Generate the tickets
        $ticket_generator = new TicketGenerator($order);
        $tickets = $ticket_generator->createTickets();

        // Data for view
        $data = [
            'tickets' => $tickets,
            'event'   => $order->event,
        ];
        try {
            PDF::loadView('Public.ViewEvent.Partials.PDFTicket', $data)->save($pdf_file['fullpath']);
            Log::info("Ticket generated!");
        } catch (\Exception $e) {
            Log::error("Error generating ticket.");
            Log::error("Error message. " . $e->getMessage());
            Log::error("Error stack trace" . $e->getTraceAsString());
            $this->fail($e);
        }

    }

    private function isAttendeeTicket()
    {
        return ($this->attendee_reference_index != null);
    }
}
