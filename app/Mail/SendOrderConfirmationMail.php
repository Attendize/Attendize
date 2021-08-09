<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\Order as OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class SendOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var Order
     */
    public $order;

    /**
     * The order service instance.
     *
     * @var OrderService
     */
    public $orderService;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order, OrderService $orderService)
    {
        $this->order = $order;
        $this->orderService = $orderService;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $file_name = $this->order->order_reference;
        $file_path = public_path(config('attendize.event_pdf_tickets_path')) . '/' . $file_name . '.pdf';

        $subject = trans(
            "Controllers.tickets_for_event",
            ["event" => $this->order->event->title]
        );
        return $this->subject($subject)
                    ->attach($file_path)
                    ->view('Emails.OrderConfirmation');
    }
}
