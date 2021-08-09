<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\EventStats;
use App\Models\Attendee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Jobs\SendAttendeeInviteJob;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class AttendeesImport implements OnEachRow, WithHeadingRow
{
    use Importable;

    public function __construct(Event $event, Ticket $ticket, bool $emailAttendees)
    {
        $this->event = $event;
        $this->ticket = $ticket;
        $this->emailAttendees = $emailAttendees;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function onRow(Row $row)
    {
        $rowArr = $row->toArray();
        $firstName = $rowArr['first_name'];
        $lastName = $rowArr['last_name'];
        $email = $rowArr['email'];

        \Log::info(sprintf("Importing attendee: %s (%s %s)", $email, $firstName, $lastName));

        // Create a new order for the attendee
        $order = Order::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'order_status_id' => config('attendize.order.complete'),
            'amount' => 0,
            'account_id' => Auth::user()->account_id,
            'event_id' => $this->event->id,
            'taxamt' => 0,
        ]);

        $orderItem = OrderItem::create([
            'title' => $this->ticket->title,
            'quantity' => 1,
            'order_id' => $order->id,
            'unit_price' => 0,
        ]);

        // Increment the ticket quantity
        $this->ticket->increment('quantity_sold');
        (new EventStats())->updateTicketsSoldCount($this->event->id, 1);

        $attendee = Attendee::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'event_id' => $this->event->id,
            'order_id' => $order->id,
            'ticket_id' => $this->ticket->id,
            'account_id' => Auth::user()->account_id,
            'reference_index' => 1,
        ]);

        if ($this->emailAttendees) {
            SendAttendeeInviteJob::dispatch($attendee);
        }

        return $attendee;
    }
}
