<?php namespace App\Http\Controllers;

use App\Cancellation\OrderCancellation;
use App\Exports\AttendeesExport;
use App\Imports\AttendeesImport;
use App\Jobs\GenerateTicketJob;
use App\Jobs\SendAttendeeInviteJob;
use App\Jobs\SendOrderAttendeeTicketJob;
use App\Jobs\SendMessageToAttendeesJob;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventStats;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Order as OrderService;
use App\Models\Ticket;
use Auth;
use Config;
use DB;
use Excel;
use Exception;
use Illuminate\Http\Request;
use Log;
use Mail;
use PDF;
use Validator;
use Illuminate\Support\Facades\Lang;

class EventAttendeesController extends MyBaseController
{
    /**
     * Show the attendees list
     *
     * @param Request $request
     * @param $event_id
     * @return View
     */
    public function showAttendees(Request $request, $event_id)
    {
        $allowed_sorts = ['first_name', 'email', 'ticket_id', 'order_reference'];

        $searchQuery = $request->get('q');
        $sort_order = $request->get('sort_order') == 'asc' ? 'asc' : 'desc';
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'created_at');

        $event = Event::scope()->find($event_id);

        if ($searchQuery) {
            $attendees = $event->attendees()
                ->withoutCancelled()
                ->join('orders', 'orders.id', '=', 'attendees.order_id')
                ->where(function ($query) use ($searchQuery) {
                    $query->where('orders.order_reference', 'like', $searchQuery . '%')
                        ->orWhere('attendees.first_name', 'like', $searchQuery . '%')
                        ->orWhere('attendees.email', 'like', $searchQuery . '%')
                        ->orWhere('attendees.last_name', 'like', $searchQuery . '%');
                })
                ->orderBy(($sort_by == 'order_reference' ? 'orders.' : 'attendees.') . $sort_by, $sort_order)
                ->select('attendees.*', 'orders.order_reference')
                ->paginate();
        } else {
            $attendees = $event->attendees()
                ->join('orders', 'orders.id', '=', 'attendees.order_id')
                ->withoutCancelled()
                ->orderBy(($sort_by == 'order_reference' ? 'orders.' : 'attendees.') . $sort_by, $sort_order)
                ->select('attendees.*', 'orders.order_reference')
                ->paginate();
        }

        $data = [
            'attendees'  => $attendees,
            'event'      => $event,
            'sort_by'    => $sort_by,
            'sort_order' => $sort_order,
            'q'          => $searchQuery ? $searchQuery : '',
        ];

        return view('ManageEvent.Attendees', $data);
    }

    /**
     * Show the 'Invite Attendee' modal
     *
     * @param Request $request
     * @param $event_id
     * @return string|View
     */
    public function showInviteAttendee(Request $request, $event_id)
    {
        $event = Event::scope()->find($event_id);

        /*
         * If there are no tickets then we can't create an attendee
         * @todo This is a bit hackish
         */
        if ($event->tickets->count() === 0) {
            return '<script>showMessage("'.trans("Controllers.addInviteError").'");</script>';
        }

        return view('ManageEvent.Modals.InviteAttendee', [
            'event'   => $event,
            'tickets' => $event->tickets()->pluck('title', 'id'),
        ]);
    }

    /**
     * Invite an attendee
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function postInviteAttendee(Request $request, $event_id)
    {
        $rules = [
            'first_name' => 'required',
            'ticket_id'  => 'required|exists:tickets,id,account_id,' . \Auth::user()->account_id,
            'email'      => 'email|required',
        ];

        $messages = [
            'ticket_id.exists'   => trans("Controllers.ticket_not_exists_error"),
            'ticket_id.required' => trans("Controllers.ticket_field_required_error"),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $ticket_id = $request->get('ticket_id');
        $event = Event::findOrFail($event_id);
        $ticket_price = 0;
        $attendee_first_name = $request->get('first_name');
        $attendee_last_name = $request->get('last_name');
        $attendee_email = $request->get('email');
        $email_attendee = $request->get('email_ticket');

        DB::beginTransaction();

        try {

            /*
             * Create the order
             */
            $order = new Order();
            $order->first_name = $attendee_first_name;
            $order->last_name = $attendee_last_name;
            $order->email = $attendee_email;
            $order->order_status_id = config('attendize.order.complete');
            $order->amount = $ticket_price;
            $order->account_id = Auth::user()->account_id;
            $order->event_id = $event_id;

            // Calculating grand total including tax
            $orderService = new OrderService($ticket_price, 0, $event);
            $orderService->calculateFinalCosts();
            $order->taxamt = $orderService->getTaxAmount();

            if ($orderService->getGrandTotal() == 0) {
                $order->is_payment_received = 1;
            }

            $order->save();

            /*
             * Update qty sold
             */
            $ticket = Ticket::scope()->find($ticket_id);
            $ticket->increment('quantity_sold');
            $ticket->increment('sales_volume', $ticket_price);

            /*
             * Insert order item
             */
            $orderItem = new OrderItem();
            $orderItem->title = $ticket->title;
            $orderItem->quantity = 1;
            $orderItem->order_id = $order->id;
            $orderItem->unit_price = $ticket_price;
            $orderItem->save();

            /*
             * Update the event stats
             */
            $event_stats = new EventStats();
            $event_stats->updateTicketsSoldCount($event_id, 1);
            $event_stats->updateTicketRevenue($ticket_id, $ticket_price);

            /*
             * Create the attendee
             */
            $attendee = new Attendee();
            $attendee->first_name = $attendee_first_name;
            $attendee->last_name = $attendee_last_name;
            $attendee->email = $attendee_email;
            $attendee->event_id = $event_id;
            $attendee->order_id = $order->id;
            $attendee->ticket_id = $ticket_id;
            $attendee->account_id = Auth::user()->account_id;
            $attendee->reference_index = 1;
            $attendee->save();


            if ($email_attendee == '1') {
                SendAttendeeInviteJob::dispatch($attendee);
            }

            session()->flash('message', trans("Controllers.attendee_successfully_invited"));

            DB::commit();

            return response()->json([
                'status'      => 'success',
                'redirectUrl' => route('showEventAttendees', [
                    'event_id' => $event_id,
                ]),
            ]);

        } catch (Exception $e) {

            Log::error($e);
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error'  => trans("Controllers.attendee_exception")
            ]);
        }

    }

    /**
     * Show the 'Import Attendee' modal
     *
     * @param Request $request
     * @param $event_id
     * @return string|View
     */
    public function showImportAttendee(Request $request, $event_id)
    {
        $event = Event::scope()->find($event_id);

        /*
         * If there are no tickets then we can't create an attendee
         * @todo This is a bit hackish
         */
        if ($event->tickets->count() === 0) {
            return '<script>showMessage("'.trans("Controllers.addInviteError").'");</script>';
        }

        return view('ManageEvent.Modals.ImportAttendee', [
            'event'   => $event,
            'tickets' => $event->tickets()->pluck('title', 'id'),
        ]);
    }


    /**
     * Import attendees
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function postImportAttendee(Request $request, $event_id)
    {
        $rules = [
            'ticket_id'      => 'required|exists:tickets,id,account_id,' . \Auth::user()->account_id,
            'attendees_list' => 'required|mimes:csv,txt|max:5000|',
        ];

        $messages = [
            'ticket_id.exists' => trans("Controllers.ticket_not_exists_error"),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);

        }

        $event = Event::findOrFail($event_id);
        $ticket = Ticket::scope()->find($request->get('ticket_id'));
        $emailAttendees = $request->get('email_ticket');
        if ($request->file('attendees_list')) {
            (new AttendeesImport($event, $ticket, (bool)$emailAttendees))->import(request()->file('attendees_list'));
        }

        session()->flash('message', 'Attendees Successfully Invited');

        return response()->json([
            'status'      => 'success',
            'redirectUrl' => route('showEventAttendees', [
                'event_id' => $event_id,
            ]),
        ]);
    }

    /**
     * Show the printable attendee list
     *
     * @param $event_id
     * @return View
     */
    public function showPrintAttendees($event_id)
    {
        $data['event'] = Event::scope()->find($event_id);
        $data['attendees'] = $data['event']->attendees()->withoutCancelled()->orderBy('first_name')->get();

        return view('ManageEvent.PrintAttendees', $data);
    }

    /**
     * Show the 'Message Attendee' modal
     *
     * @param Request $request
     * @param $attendee_id
     * @return View
     */
    public function showMessageAttendee(Request $request, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee' => $attendee,
            'event'    => $attendee->event,
        ];

        return view('ManageEvent.Modals.MessageAttendee', $data);
    }

    /**
     * Send a message to an attendee
     *
     * @param Request $request
     * @param $attendee_id
     * @return mixed
     */
    public function postMessageAttendee(Request $request, $attendee_id)
    {
        $rules = [
            'subject' => 'required',
            'message' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee'        => $attendee,
            'message_content' => $request->get('message'),
            'subject'         => $request->get('subject'),
            'event'           => $attendee->event
        ];

        //@todo move this to the SendAttendeeMessage Job
        Mail::send(Lang::locale().'.Emails.messageReceived', $data, function ($message) use ($attendee, $data) {
            $message->to($attendee->email, $attendee->full_name)
                ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                ->subject($data['subject']);
        });

        /* Could bcc in the above? */
        if ($request->get('send_copy') == '1') {
            Mail::send(Lang::locale().'.Emails.messageReceived', $data, function ($message) use ($attendee, $data) {
                $message->to($attendee->event->organiser->email, $attendee->event->organiser->name)
                    ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                    ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                    ->subject($data['subject'] . trans("Email.organiser_copy"));
            });
        }

        return response()->json([
            'status'  => 'success',
            'message' => trans("Controllers.message_successfully_sent"),
        ]);
    }

    /**
     * Shows the 'Message Attendees' modal
     *
     * @param $event_id
     * @return View
     */
    public function showMessageAttendees(Request $request, $event_id)
    {
        $data = [
            'event'   => Event::scope()->find($event_id),
            'tickets' => Event::scope()->find($event_id)->tickets()->pluck('title', 'id')->toArray(),
        ];

        return view('ManageEvent.Modals.MessageAttendees', $data);
    }

    /**
     * Send a message to attendees
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function postMessageAttendees(Request $request, $event_id)
    {
        $rules = [
            'subject'    => 'required',
            'message'    => 'required',
            'recipients' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $message = Message::createNew();
        $message->message = $request->get('message');
        $message->subject = $request->get('subject');
        $message->recipients = ($request->get('recipients') == 'all') ? 'all' : $request->get('recipients');
        $message->event_id = $event_id;
        $message->save();

        /*
         * Queue the emails
         */
        SendMessageToAttendeesJob::dispatch($message);

        return response()->json([
            'status'  => 'success',
            'message' => 'Message Successfully Sent',
        ]);
    }

    /**
     * @param $event_id
     * @param $attendee_id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function showExportTicket($event_id, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);
        $attendee_reference = $attendee->getReferenceAttribute();

        Log::debug("Exporting ticket PDF", [
            'attendee_id' => $attendee_id,
            'order_reference' => $attendee->order->order_reference,
            'attendee_reference' => $attendee_reference,
            'event_id' => $event_id
        ]);

        $pdf_file = public_path(config('attendize.event_pdf_tickets_path')) . '/' . $attendee_reference . '.pdf';

        $this->dispatchNow(new GenerateTicketJob($attendee));

        return response()->download($pdf_file);
    }

    /**
     * Downloads an export of attendees
     *
     * @param $event_id
     * @param string $export_as (xlsx, xls, csv, html)
     */
    public function showExportAttendees($event_id, $export_as = 'xls')
    {
        $event = Event::scope()->findOrFail($event_id);
        $date = date('d-m-Y-g.i.a');
        return (new AttendeesExport($event->id))->download("attendees-as-of-{$date}.{$export_as}");
    }

    /**
     * Show the 'Edit Attendee' modal
     *
     * @param Request $request
     * @param $event_id
     * @param $attendee_id
     * @return View
     */
    public function showEditAttendee(Request $request, $event_id, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee' => $attendee,
            'event'    => $attendee->event,
            'tickets'  => $attendee->event->tickets->pluck('title', 'id'),
        ];

        return view('ManageEvent.Modals.EditAttendee', $data);
    }

    /**
     * Updates an attendee
     *
     * @param Request $request
     * @param $event_id
     * @param $attendee_id
     * @return mixed
     */
    public function postEditAttendee(Request $request, $event_id, $attendee_id)
    {
        $rules = [
            'first_name' => 'required',
            'ticket_id'  => 'required|exists:tickets,id,account_id,' . Auth::user()->account_id,
            'email'      => 'required|email',
        ];

        $messages = [
            'ticket_id.exists'   => trans("Controllers.ticket_not_exists_error"),
            'ticket_id.required' => trans("Controllers.ticket_field_required_error"),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $attendee = Attendee::scope()->findOrFail($attendee_id);
        $attendee->update($request->all());

        session()->flash('message',trans("Controllers.successfully_updated_attendee"));

        return response()->json([
            'status'      => 'success',
            'id'          => $attendee->id,
            'redirectUrl' => '',
        ]);
    }

    /**
     * Shows the 'Cancel Attendee' modal
     *
     * @param Request $request
     * @param $event_id
     * @param $attendee_id
     * @return View
     */
    public function showCancelAttendee(Request $request, $event_id, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee' => $attendee,
            'event'    => $attendee->event,
            'tickets'  => $attendee->event->tickets->pluck('title', 'id'),
        ];

        return view('ManageEvent.Modals.CancelAttendee', $data);
    }

    /**
     * Cancels an attendee
     *
     * @param Request $request
     * @param $event_id
     * @param $attendee_id
     * @return mixed
     */
    public function postCancelAttendee(Request $request, $event_id, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);
        if ($attendee->is_cancelled) {
            return response()->json([
                'status' => 'success',
                'message' => trans("Controllers.attendee_already_cancelled"),
            ]);
        }

        // Create email data
        $data = [
            'attendee' => $attendee,
            'email_logo' => $attendee->event->organiser->full_logo_path,
        ];

        try {
            // Cancels attendee for an order and attempts to refund
            $orderCancellation = OrderCancellation::make($attendee->order, collect([$attendee]));
            $orderCancellation->cancel();
            $data['refund_amount'] = $orderCancellation->getRefundAmount();
        } catch (Exception | OrderRefundException $e) {
            Log::error($e);
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        if ($request->get('notify_attendee') == '1') {
            try {
                Mail::send(Lang::locale().'.Emails.notifyCancelledAttendee', $data, function ($message) use ($attendee) {
                    $message->to($attendee->email, $attendee->full_name)
                        ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                        ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                        ->subject(trans("Email.your_ticket_cancelled"));
                });
            } catch (\Exception $e) {
                Log::error($e);
                // We do not want to kill the flow if the email fails
            }
        }

        try {
            // Let the user know that they have received a refund.
            Mail::send(Lang::locale().'.Emails.notifyRefundedAttendee', $data, function ($message) use ($attendee) {
                $message->to($attendee->email, $attendee->full_name)
                    ->from(config('attendize.outgoing_email_noreply'), $attendee->event->organiser->name)
                    ->replyTo($attendee->event->organiser->email, $attendee->event->organiser->name)
                    ->subject(trans("Email.refund_from_name", ["name"=>$attendee->event->organiser->name]));
            });
        } catch (\Exception $e) {
            Log::error($e);
            // We do not want to kill the flow if the email fails
        }

        session()->flash('message', trans("Controllers.successfully_cancelled_attendee"));

        return response()->json([
            'status' => 'success',
            'id' => $attendee->id,
            'redirectUrl' => '',
        ]);
    }

    /**
     * Show the 'Message Attendee' modal
     *
     * @param Request $request
     * @param $attendee_id
     * @return View
     */
    public function showResendTicketToAttendee(Request $request, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'attendee' => $attendee,
            'event'    => $attendee->event,
        ];

        return view('ManageEvent.Modals.ResendTicketToAttendee', $data);
    }

    /**
     * Send a message to an attendee
     *
     * @param Request $request
     * @param $attendee_id
     * @return mixed
     */
    public function postResendTicketToAttendee(Request $request, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $this->dispatch(new SendOrderAttendeeTicketJob($attendee));

        return response()->json([
            'status'  => 'success',
            'message' => trans("Controllers.ticket_successfully_resent"),
        ]);
    }


    /**
     * Show an attendee ticket
     *
     * @param Request $request
     * @param $attendee_id
     * @return bool
     */
    public function showAttendeeTicket(Request $request, $attendee_id)
    {
        $attendee = Attendee::scope()->findOrFail($attendee_id);

        $data = [
            'order'     => $attendee->order,
            'event'     => $attendee->event,
            'tickets'   => $attendee->ticket,
            'attendees' => [$attendee],
            'css'       => file_get_contents(public_path('assets/stylesheet/ticket.css')),
            'image'     => base64_encode(file_get_contents(public_path($attendee->event->organiser->full_logo_path))),

        ];

        if ($request->get('download') == '1') {
            return PDF::html('Public.ViewEvent.Partials.PDFTicket', $data, 'Tickets');
        }
        return view('Public.ViewEvent.Partials.PDFTicket', $data);
    }

}


