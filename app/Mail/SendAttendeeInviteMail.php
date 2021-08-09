<?php

namespace App\Mail;

use App\Models\Attendee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Log;

class SendAttendeeInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $attendee;

    /**
     * Create a new message instance.
     *
     * @return void
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
        Log::debug("Sending invite to: " . $this->attendee->email);

        $subject = trans("Email.your_ticket_for_event", ["event" => $this->attendee->order->event->title]);
        $file_name = $this->attendee->getReferenceAttribute();
        $file_path = public_path(config('attendize.event_pdf_tickets_path')) . '/' . $file_name . '.pdf';

        return $this->subject($subject)
                    ->attach($file_path)
                    ->view('Emails.AttendeeInvite');
    }
}
