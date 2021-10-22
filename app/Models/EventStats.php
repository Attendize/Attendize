<?php

namespace App\Models;

use Cookie;
use DB;

class EventStats extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static $unguarded = true;

    /**
     * @todo This shouldn't be in a view.
     * Update the amount of revenue a ticket has earned.
     *
     * @param int   $ticket_id
     * @param float $amount
     * @param bool  $deduct
     *
     * @return bool
     */
    public function updateTicketRevenue($ticket_id, $amount, $deduct = false)
    {
        $ticket = Ticket::find($ticket_id);

        if ($deduct) {
            $amount = $amount * -1;
        }

        $ticket->sales_volume = $ticket->sales_volume + $amount;

        return $ticket->save();
    }

    /**
     * Update the amount of views a ticket has earned.
     *
     * @param $event_id
     *
     * @return bool
     */
    public function updateViewCount($event_id)
    {
        $stats = $this->updateOrCreate([
            'event_id' => $event_id,
            'date'     => DB::raw('CURRENT_DATE'),
        ]);

        $cookie_name = 'visitTrack_'.$event_id.'_'.date('dmy');

        if (! Cookie::get($cookie_name)) {
            Cookie::queue($cookie_name, true, 60 * 24 * 14);
            $stats->unique_views++;
        }

        $stats->views++;

        return $stats->save();
    }

    /**
     * @todo: Missing amount?
     * Updates the sales volume earned by an event.
     */
    public function updateSalesVolume($event_id)
    {
        $stats = $this->updateOrCreate([
            'event_id' => $event_id,
            'date'     => DB::raw('CURRENT_DATE'),
        ]);

        $stats->sales_volume = $stats->sales_volume + $amount;

        return $stats->save();
    }

    /**
     * Updates the number of tickets sold for the event.
     *
     * @param $event_id
     * @param $count
     *
     * @return bool
     */
    public function updateTicketsSoldCount($event_id, $count)
    {
        $stats = $this->updateOrCreate([
            'event_id' => $event_id,
            'date'     => DB::raw('CURRENT_DATE'),
        ]);

        $stats->increment('tickets_sold', $count);

        return $stats->save();
    }
}
