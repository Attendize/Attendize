@extends('Emails.Layouts.Master')

@section('message_content')

@lang("basic.hello") {{ $attendee->first_name }},<br><br>

{{ @trans("Order_Emails.tickets_attached") }} <a href="{{public_route('showOrderDetails', ['order_reference' => $attendee->order->order_reference])}}">{{public_route('showOrderDetails', ['order_reference' => $attendee->order->order_reference])}}</a>.

@stop
