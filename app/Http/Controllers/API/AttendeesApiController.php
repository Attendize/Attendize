<?php

namespace App\Http\Controllers\API;

use App\Models\Attendee;
use Illuminate\Http\Request;

class AttendeesApiController extends ApiBaseController
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return Attendee::scope($this->account_id)->paginate($request->get('per_page', 25));
    }

    /**
     * @param Request $request
     * @param $attendee_id
     * @return mixed
     */
    public function show(Request $request, $attendee_id)
    {
        if ($attendee_id) {
            return Attendee::scope($this->account_id)->find($attendee_id);
        }

        return response('Attendee Not Found', 404);
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
