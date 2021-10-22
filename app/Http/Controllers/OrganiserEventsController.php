<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organiser;
use Illuminate\Http\Request;

class OrganiserEventsController extends MyBaseController
{
    /**
     * Show the organiser events page
     *
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function showEvents(Request $request, $organiser_id)
    {
        $organiser = Organiser::scope()->findOrfail($organiser_id);

        $allowed_sorts = ['created_at', 'start_date', 'end_date', 'title'];

        $searchQuery = $request->get('q');
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'start_date');

        $events = $searchQuery
            ? Event::scope()->with(['organiser', 'currency'])->where('title', 'like', '%'.$searchQuery.'%')->orderBy($sort_by,
                'desc')->where('organiser_id', '=', $organiser_id)->paginate(12)
            : Event::scope()->with(['organiser', 'currency'])->where('organiser_id', '=', $organiser_id)->orderBy($sort_by, 'desc')->paginate(12);

        $data = [
            'events'    => $events,
            'organiser' => $organiser,
            'search'    => [
                'q'        => $searchQuery ? $searchQuery : '',
                'sort_by'  => $request->get('sort_by') ? $request->get('sort_by') : '',
                'showPast' => $request->get('past'),
            ],
        ];

        return view('ManageOrganiser.Events', $data);
    }
}
