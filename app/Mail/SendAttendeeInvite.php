<?php

namespace App\Mail;

use App\Generators\TicketGenerator;
use App\Models\Attendee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAttendeeInvite extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Attendee $attendee */
    public $attendee;

    /**
     * Create a new message instance.
     *
     * @param  Attendee  $attendee
     */
    public function __construct(Attendee $attendee)
    {
        $this->attendee = $attendee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('Sending invite to: ' . $this->attendee->email);

        $pdf_file = TicketGenerator::createPDFTicket($this->attendee->order, $this->attendee);

        return $this->subject(
            trans('Email.your_ticket_for_event', ['event' => $this->attendee->order->event->title])
        )
            ->view('Mailers.TicketMailer.SendAttendeeInvite')
            ->attach($pdf_file->path);
    }
}
