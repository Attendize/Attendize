@extends('Emails.Layouts.Master')

@section('message_content')
@lang("basic.hello"),<br><br>

@lang("Order_Emails.received_new_order") <b>{{$order->event->title}}</b>.<br><br>

@if(!$order->is_payment_received)
    <b>@lang("Order_Emails.order_still_awaiting_payment")</b>
    <br><br>
@endif


<h3>@lang("Public_ViewEvent.order_summary")</h3>
@lang("Email.organiser_copy")<br>
@lang("Public_ViewEvent.order_reference"): <b>{{$order->order_reference}}</b><br>
@lang("Public_ViewEvent.order_name"): <b>{{$order->full_name}}</b><br>
@lang("Public_ViewEvent.order_date"): <b>{{$order->created_at->format(config('attendize.default_datetime_format'))}}</b><br>
@lang("Public_ViewEvent.order_email"): <b>{{$order->email}}</b><br>
@if ($order->is_business)
<h3>@lang("Public_ViewEvent.business_details")</h3>
@if ($order->business_name) @lang("Public_ViewEvent.business_name"): <strong>{{$order->business_name}}</strong><br>@endif
@if ($order->business_tax_number) @lang("Public_ViewEvent.business_tax_number"): <strong>{{$order->business_tax_number}}</strong><br>@endif
@if ($order->business_address_line_one) @lang("Public_ViewEvent.business_address_line1"): <strong>{{$order->business_address_line_one}}</strong><br>@endif
@if ($order->business_address_line_two) @lang("Public_ViewEvent.business_address_line2"): <strong>{{$order->business_address_line_two}}</strong><br>@endif
@if ($order->business_address_state_province) @lang("Public_ViewEvent.business_address_state_province"): <strong>{{$order->business_address_state_province}}</strong><br>@endif
@if ($order->business_address_city) @lang("Public_ViewEvent.business_address_city"): <strong>{{$order->business_address_city}}</strong><br>@endif
@if ($order->business_address_code) @lang("Public_ViewEvent.business_address_code"): <strong>{{$order->business_address_code}}</strong><br>@endif
@endif

<h3>@lang("Public_ViewEvent.order_items")</h3>
<div style="padding:10px; background: #F9F9F9; border: 1px solid #f1f1f1;">

    <table style="width:100%; margin:10px;">
        <tr>
            <th>
                @lang("Public_ViewEvent.ticket")
            </th>
            <th>
                @lang("Public_ViewEvent.quantity_full")
            </th>
            <th>
                @lang("Public_ViewEvent.price")
            </th>
            <th>
                @lang("Public_ViewEvent.booking_fee")
            </th>
            <th>
                @lang("Public_ViewEvent.total")
            </th>
        </tr>
        @foreach($order->orderItems as $order_item)
        <tr>
            <td>
                {{$order_item->title}}
            </td>
            <td>
                {{$order_item->quantity}}
            </td>
            <td>
                @isFree($order_item->unit_price)
                @lang("Public_ViewEvent.free")
                @else
                {{money($order_item->unit_price, $order->event->currency)}}
                @endif
            </td>
            <td>
                @requiresPayment($order_item->unit_booking_fee)
                    @isFree($order_item->unit_price)
                    -
                    @else
                    {{money($order_item->unit_booking_fee, $order->event->currency)}}
                    @endif
                @else
                    -
                @endif
            </td>
            <td>
                @isFree($order_item->unit_price)
                @lang("Public_ViewEvent.free")
                @else
                {{money(($order_item->unit_price + $order_item->unit_booking_fee) * ($order_item->quantity), $order->event->currency)}}
                @endif

            </td>
        </tr>
        @endforeach
        <tr>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
                <b>@lang("Public_ViewEvent.sub_total")</b>
            </td>
            <td colspan="2">
                {{$orderService->getOrderTotalWithBookingFee(true)}}
            </td>
        </tr>
        @if($order->event->organiser->charge_tax == 1)
        <tr>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
                <strong>{{$order->event->organiser->tax_name}}</strong><em>({{$order->event->organiser->tax_value}}%)</em>
            </td>
            <td colspan="2">
                {{$orderService->getTaxAmount(true)}}
            </td>
        </tr>
        @endif
        <tr>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
                <b>@lang("Public_ViewEvent.total")</b>
            </td>
            <td colspan="2">
                {{$orderService->getGrandTotal(true)}}
            </td>
        </tr>
    </table>


    <br><br>
    @lang("Order_Emails.manage_order") <a href="{{route('showEventOrders', ['event_id' => $order->event->id, 'q'=>$order->order_reference])}}">{{route('showEventOrders', ['event_id' => $order->event->id, 'q'=>$order->order_reference])}}</a>
    <br><br>
</div>
<br><br>
@lang("basic.thank_you")
@stop
