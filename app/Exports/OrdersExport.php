<?php

namespace App\Exports;

use App\Models\Order;
use DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\BeforeExport;

class OrdersExport implements FromQuery, WithHeadings, WithEvents
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

        $query = Order::query()->where('event_id', $this->event_id);
        $query->select([
            'orders.first_name',
            'orders.last_name',
            'orders.email',
            'orders.order_reference',
            'orders.amount',
            DB::raw("(CASE WHEN orders.is_refunded = 1 THEN '$yes' ELSE '$no' END) AS `orders.is_refunded`"),
            DB::raw("(CASE WHEN orders.is_partially_refunded = 1 THEN '$yes' ELSE '$no' END) AS `orders.is_partially_refunded`"),
            'orders.amount_refunded',
            'orders.created_at',
        ]);

        return $query;
    }

    public function headings(): array
    {
        return [
            trans('Attendee.first_name'),
            trans('Attendee.last_name'),
            trans('Attendee.email'),
            trans('Order.order_ref'),
            trans('Order.amount'),
            trans('Order.fully_refunded'),
            trans('Order.partially_refunded'),
            trans('Order.amount_refunded'),
            trans('Order.order_date'),
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
