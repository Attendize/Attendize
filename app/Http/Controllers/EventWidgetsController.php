<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

/*
  Attendize.com   - Event Management & Ticketing
 */

class EventWidgetsController extends MyBaseController
{
    /**
     * Show the event widgets page
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function showEventWidgets(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $data = [
            'event' => $event,
        ];

        return view('ManageEvent.Widgets', $data);
    }
}
