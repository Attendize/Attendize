<?php

namespace App\Mail;

use App\Generators\TicketGenerator;
use App\Models\Order;
use App\Services\Order as OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderTickets extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Order $order */
    public $order;

    /** @var OrderService $orderService */
    public $orderService;

    /**
     * Create a new message instance.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->orderService = new OrderService(
            $this->order->amount, $this->order->organiser_booking_fee, $this->order->event
        );

        $this->orderService->calculateFinalCosts();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('Sending order tickets to: ' . $this->order->email);

        // Get PDF filename and path
        $pdf_file = TicketGenerator::createPDFTicket($this->order);

        if (!file_exists($pdf_file->path)) {
            Log::error('Cannot send actual ticket to : ' . $this->order->email . ' as ticket file does not exist on disk');
            return null;
        }

        return $this->view('Mailers.TicketMailer.SendOrderTickets')
            ->subject(
                trans('Controllers.tickets_for_event', ['event' => $this->order->event->title])
            )
            ->attach($pdf_file->path);
    }
}
