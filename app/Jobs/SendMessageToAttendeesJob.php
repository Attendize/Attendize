<?php

namespace App\Jobs;

use App\Mail\SendMessageToAttendeesMail;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Config;
use Mail;

class SendMessageToAttendeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->message->recipients == 'all') {
            $recipients = $this->message->event->attendees;
        } else {
            $recipients = Attendee::where('ticket_id', '=', $this->message->recipients)->where('account_id', '=', $this->message->account_id)->get();
        }

        $event = $this->message->event;

        foreach ($recipients as $attendee) {
            if ($attendee->is_cancelled) {
               continue;
            }

            $mail = new SendMessageToAttendeesMail($this->message->subject, $this->message->message, $event, $attendee);
            Mail::to($attendee->email, $attendee->full_name)
                ->locale(Config::get('app.locale'))
                ->send($mail);
        }

        $this->message->is_sent = 1;
        $this->message->sent_at = Carbon::now();
        $this->message->save();
    }
}
