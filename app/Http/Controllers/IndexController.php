<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class IndexController extends Controller
{
    /**
     * redirect index page
     * @param  Request $request http request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showIndex(Request $request)
    {
        /** @var $user App\Models\User */
        $user = Auth::user();

        // super admin users will get shown the organiser selection page
        if ($user->can('manage organisers')) {
            return redirect()->route('showSelectOrganiser');
        }

        $isCheckinUser = $user->hasRole('attendee check in');

        // Normal users will get shown their dashboard
        $organiser = $user->organiser;
        $allowed_sorts = ['created_at', 'start_date', 'end_date', 'title'];

        $searchQuery = $request->get('q');
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'start_date');

        // If user can manage events, then they can see all events, otherwise just their own
        $events = $organiser->events()
            ->where('organiser_id', $organiser->id)
            ->orderBy($sort_by, 'desc');

        // We only want to filter for normal users here. Check in users get a limited event UI
        if (!$user->can('manage events') && !$isCheckinUser) {
            $events->where('user_id', $user->id);
        }

        if ($searchQuery) {
            $events->where('title', 'like', '%' . $searchQuery . '%');
        }

        $data = [
            'events' => $events->paginate(12),
            'organiser' => $organiser,
            'search' => [
                'q' => $searchQuery ? $searchQuery : '',
                'sort_by' => $request->get('sort_by') ? $request->get('sort_by') : '',
                'showPast' => $request->get('past'),
            ],
        ];

        return view('ManageOrganiser.Events', $data);
    }
}
