<?php

namespace App\Exports;

use App\Models\Attendee;
use Auth;
use DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\BeforeExport;

class AttendeesExport implements FromQuery, WithHeadings, WithEvents
{
    use Exportable;

    public function __construct(int $event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return \Illuminate\Support\Query
     */
    public function query()
    {
        $yes = strtoupper(trans('basic.yes'));
        $no = strtoupper(trans('basic.no'));
        $query = Attendee::query()->select([
            'attendees.first_name',
            'attendees.last_name',
            'attendees.email',
            'attendees.private_reference_number',
            'orders.order_reference',
            'tickets.title',
            'orders.created_at',
            DB::raw("(CASE WHEN attendees.has_arrived THEN '$yes' ELSE '$no' END) AS has_arrived"),
            'attendees.arrival_time',
        ])->join('events', 'events.id', '=', 'attendees.event_id')
            ->join('orders', 'orders.id', '=', 'attendees.order_id')
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->where('attendees.event_id', $this->event_id)
            ->where('attendees.account_id', Auth::user()->account_id)
            ->where('attendees.is_cancelled', false);

        return $query;
    }

    public function headings(): array
    {
        return [
            trans('Attendee.first_name'),
            trans('Attendee.last_name'),
            trans('Attendee.email'),
            trans('Ticket.id'),
            trans('Order.order_ref'),
            trans('Ticket.ticket_type'),
            trans('Order.order_date'),
            trans('Attendee.has_arrived'),
            trans('Attendee.arrival_time'),
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->getProperties()->setCreator(config('attendize.app_name'));
                $event->writer->getProperties()->setCompany(config('attendize.app_name'));
            },
        ];
    }
}
