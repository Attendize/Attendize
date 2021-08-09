<?php

namespace App\Http\Controllers;

use App\Attendize\Utils;
use App\Models\Affiliate;
use App\Models\Event;
use App\Models\EventAccessCodes;
use App\Models\EventStats;
use Auth;
use Cookie;
use Illuminate\Http\Request;
use Mail;
use Redirect;
use Validator;
use Services\Captcha\Factory;
use Illuminate\Support\Facades\Lang;

class EventViewController extends Controller
{
    protected $captchaService;

    public function __construct()
    {
        $captchaConfig = config('attendize.captcha');
        if ($captchaConfig["captcha_is_on"]) {
            $this->captchaService = Factory::create($captchaConfig);
        }
    }

    /**
     * Show the homepage for an event
     *
     * @param Request $request
     * @param $event_id
     * @param string $slug
     * @param bool $preview
     * @return mixed
     */
    public function showEventHome(Request $request, $event_id, $slug = '', $preview = false)
    {
        $event = Event::findOrFail($event_id);

        if (!Utils::userOwns($event) && !$event->is_live) {
            return view('Public.ViewEvent.EventNotLivePage');
        }

        $data = [
            'event' => $event,
            'tickets' => $event->tickets()->orderBy('sort_order', 'asc')->get(),
            'is_embedded' => 0,
        ];
        /*
         * Don't record stats if we're previewing the event page from the backend or if we own the event.
         */
        if (!$preview && !Auth::check()) {
            $event_stats = new EventStats();
            $event_stats->updateViewCount($event_id);
        }

        /*
         * See if there is an affiliate referral in the URL
         */
        if ($affiliate_ref = $request->get('ref')) {
            $affiliate_ref = preg_replace("/\W|_/", '', $affiliate_ref);

            if ($affiliate_ref) {
                $affiliate = Affiliate::firstOrNew([
                    'name'       => $request->get('ref'),
                    'event_id'   => $event_id,
                    'account_id' => $event->account_id,
                ]);

                ++$affiliate->visits;

                $affiliate->save();

                Cookie::queue('affiliate_' . $event_id, $affiliate_ref, 60 * 24 * 60);
            }
        }

        return view('Public.ViewEvent.EventPage', $data);
    }

    /**
     * Show preview of event homepage / used for backend previewing
     *
     * @param $event_id
     * @return mixed
     */
    public function showEventHomePreview($event_id)
    {
        return showEventHome($event_id, true);
    }

    /**
     * Sends a message to the organiser
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function postContactOrganiser(Request $request, $event_id)
    {
        $rules = [
            'name'                  => 'required',
            'email'                 => 'required|email',
            'message'               => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        if (is_object($this->captchaService)) {
            if (!$this->captchaService->isHuman($request)) {
                return Redirect::back()
                    ->with(['message' => trans("Controllers.incorrect_captcha"), 'failed' => true])
                    ->withInput();
            }
        }

        $event = Event::findOrFail($event_id);

        $data = [
            'sender_name'     => $request->get('name'),
            'sender_email'    => $request->get('email'),
            'message_content' => clean($request->get('message')),
            'event'           => $event,
        ];

        Mail::send(Lang::locale().'.Emails.messageReceived', $data, function ($message) use ($event, $data) {
            $message->to($event->organiser->email, $event->organiser->name)
                ->from(config('attendize.outgoing_email_noreply'), $data['sender_name'])
                ->replyTo($data['sender_email'], $data['sender_name'])
                ->subject(trans("Email.message_regarding_event", ["event"=>$event->title]));
        });

        return response()->json([
            'status'  => 'success',
            'message' => trans("Controllers.message_successfully_sent"),
        ]);
    }

    public function showCalendarIcs(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        $icsContent = $event->getIcsForEvent();

        return response()->make($icsContent, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="event.ics'
        ]);
    }

    /**
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postShowHiddenTickets(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        $accessCode = strtoupper($request->get('access_code'));
        if (!$accessCode) {
            return response()->json([
                'status' => 'error',
                'message' => trans('AccessCodes.valid_code_required'),
            ]);
        }

        $unlockedHiddenTickets = $event->tickets()
            ->where('is_hidden', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->filter(function($ticket) use ($accessCode) {
                // Only return the hidden tickets that match the access code
                return ($ticket->event_access_codes()->where('code', $accessCode)->get()->count() > 0);
            });

        if ($unlockedHiddenTickets->count() === 0) {
            return response()->json([
                'status' => 'error',
                'message' => trans('AccessCodes.no_tickets_matched'),
            ]);
        }

        // Bump usage count
        EventAccessCodes::logUsage($event_id, $accessCode);

        return view('Public.ViewEvent.Partials.EventHiddenTicketsSelection', [
            'event' => $event,
            'tickets' => $unlockedHiddenTickets,
            'is_embedded' => 0,
        ]);
    }
}
