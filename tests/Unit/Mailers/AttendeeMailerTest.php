<?php

namespace Tests\Unit\Mailers;

use App\Generators\TicketGenerator;
use App\Mail\SendAttendeeInvite;
use App\Mail\SendAttendeeTicket;
use App\Mailers\AttendeeMailer;
use App\Models\Attendee;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\File\File as FileResponse;
use Tests\TestCase;

class AttendeeMailerTest extends TestCase
{

    public function testSendAttendeeTicket()
    {
        Mail::fake();

        // Assert that no mailables were sent...
        Mail::assertNothingSent();

        $this->withOrganiser();

        $attendee = factory(Attendee::class)->create(['account_id' => $this->organiser->account_id]);

        tap(new AttendeeMailer())->sendAttendeeTicket($attendee);

        Mail::assertSent(SendAttendeeTicket::class, static function (SendAttendeeTicket $mail) use ($attendee) {
            $mail->build();

            return $mail->attendee === $attendee &&
                $mail->attendee->order === $attendee->order &&
                $mail->hasTo($attendee->email) &&
                count($mail->attachments) > 0;
        });

        $pdf_file = TicketGenerator::generateFileName($attendee->order, $attendee);

        /** @var FileResponse $file */
        $this->assertFileExists($pdf_file->path);

        $this->assertTrue(File::delete($pdf_file->path));
    }

    public function testSendAttendeeInvite()
    {
        Mail::fake();

        // Assert that no mailables were sent...
        Mail::assertNothingSent();

        $this->withOrganiser();

        $attendee = factory(Attendee::class)->create(['account_id' => $this->organiser->account_id]);

        tap(new AttendeeMailer())->sendAttendeeInvite($attendee);

        Mail::assertSent(SendAttendeeInvite::class, static function (Mailable $mail) use ($attendee) {
            $mail->build();

            return $mail->attendee === $attendee &&
                $mail->attendee->order === $attendee->order &&
                $mail->hasTo($attendee->email) &&
                count($mail->attachments) > 0;
        });

        $pdf_file = TicketGenerator::generateFileName($attendee->order, $attendee);

        /** @var FileResponse $file */
        $this->assertFileExists($pdf_file->path);

        $this->assertTrue(File::delete($pdf_file->path));
    }
}
