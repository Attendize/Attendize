<?php

namespace Tests\Unit\Mailers;

use App\Generators\TicketGenerator;
use App\Mail\SendAttendeeInvite;
use App\Mail\SendAttendeeTicket;
use App\Mail\SendOrderTickets;
use App\Mailers\AttendeeMailer;
use App\Mailers\OrderMailer;
use App\Models\Attendee;
use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\File\File as FileResponse;
use Tests\TestCase;

class OrderMailerTest extends TestCase
{

    public function testSendOrderTickets()
    {
        Mail::fake();

        // Assert that no mailables were sent...
        Mail::assertNothingSent();

        $this->withOrganiser();

        $order = factory(Order::class)->create();

        tap(new OrderMailer())->sendOrderTickets($order);

        Mail::assertSent(SendOrderTickets::class, static function (SendOrderTickets $mail) use ($order) {
            $mail->build();

            return $mail->order === $order &&
                $mail->hasTo($order->email) &&
                count($mail->attachments) > 0;
        });

        $pdf_file = TicketGenerator::generateFileName($order);

        /** @var FileResponse $file */
        $this->assertFileExists($pdf_file->path);

        $this->assertTrue(File::delete($pdf_file->path));
    }
}
