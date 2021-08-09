<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use PDF;

class GenerateTicketsJobBase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $attendee;
    public $event;
    public $order;
    public $file_name;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_path = public_path(config('attendize.event_pdf_tickets_path')) . '/' . $this->file_name;
        $file_with_ext = $file_path . '.pdf';

        if (file_exists($file_with_ext)) {
            Log::info("Use ticket from cache: " . $file_with_ext);
            return;
        }

        $organiser = $this->event->organiser;
        $image_path = $organiser->full_logo_path;
        $images = [];
        $imgs = $this->event->images;
        foreach ($imgs as $img) {
            $images[] = base64_encode(file_get_contents(public_path($img->image_path)));
        }

        $data = [
            'order'     => $this->order,
            'event'     => $this->event,
            'attendees' => $this->attendees,
            'css'       => file_get_contents(public_path('assets/stylesheet/ticket.css')),
            'image'     => base64_encode(file_get_contents(public_path($image_path))),
            'images'    => $images,
        ];
        try {
            PDF::setOutputMode('F'); // force to file
            PDF::html('Public.ViewEvent.Partials.PDFTicket', $data, $file_path);
            Log::info("Ticket generated!");
        } catch(\Exception $e) {
            Log::error("Error generating ticket. This can be due to permissions on vendor/nitmedia/wkhtml2pdf/src/Nitmedia/Wkhtml2pdf/lib. This folder requires write and execute permissions for the web user");
            Log::error("Error message. " . $e->getMessage());
            Log::error("Error stack trace" . $e->getTraceAsString());
            $this->fail($e);
        }
    }
}
