<?php

namespace Tests\Unit\Jobs;

use App\Generators\TicketGenerator;
use App\Jobs\GenerateTicket;
use App\Models\Attendee;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\File\File as FileResponse;
use Tests\TestCase;

class GenerateTicketTest extends TestCase
{
    public function testGenerateOrderTicket()
    {
        Queue::fake();

        // Assert that no jobs were pushed...
        Queue::assertNothingPushed();

        $this->withOrganiser();

        // We need at least one attendee for create ticket
        $attendee = factory(Attendee::class)->create();

        GenerateTicket::dispatch($attendee->order);

        // Perform Generate Ticket...
        Queue::assertPushed(GenerateTicket::class, static function (GenerateTicket $job) use ($attendee) {
            return $job->order === $attendee->order &&
                $job->attendee === null;
        });

        GenerateTicket::dispatchNow($attendee->order);

        $pdf_file = TicketGenerator::generateFileName($attendee->order);

        /** @var FileResponse $file */
        $this->assertFileExists($pdf_file->path);

        $this->assertTrue(File::delete($pdf_file->path));
    }

    public function testGenerateAttendeeTicket()
    {
        Queue::fake();

        // Assert that no jobs were pushed...
        Queue::assertNothingPushed();

        $this->withOrganiser();

        // We need at least one attendee for create ticket
        $attendee = factory(Attendee::class)->create();

        GenerateTicket::dispatch($attendee->order, $attendee);

        // Perform Generate Ticket...
        Queue::assertPushed(GenerateTicket::class, static function (GenerateTicket $job) use ($attendee) {
            return $job->order === $attendee->order &&
                $job->attendee === $attendee;
        });

        GenerateTicket::dispatchNow($attendee->order, $attendee);

        $pdf_file = TicketGenerator::generateFileName($attendee->order, $attendee);

        /** @var FileResponse $file */
        $this->assertFileExists($pdf_file->path);

        $this->assertTrue(File::delete($pdf_file->path));
    }
}
