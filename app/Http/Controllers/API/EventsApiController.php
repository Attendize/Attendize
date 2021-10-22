<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use Illuminate\Http\Request;

class EventsApiController extends ApiBaseController
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return Event::scope($this->account_id)->paginate(20);
    }

    /**
     * @param Request $request
     * @param $attendee_id
     * @return mixed
     */
    public function show(Request $request, $attendee_id)
    {
        if ($attendee_id) {
            return Event::scope($this->account_id)->find($attendee_id);
        }

        return response('Event Not Found', 404);
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request)
    {
    }

    public function destroy(Request $request)
    {
    }
}
