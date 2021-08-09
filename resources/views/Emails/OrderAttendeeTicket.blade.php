@extends('Emails.Layouts.Master')

@section('message_content')

@lang("basic.hello") {{ $attendee->first_name }},<br><br>

{{ @trans("Order_Emails.tickets_attached") }} <a href="{{route('showOrderDetails', ['order_reference' => $attendee->order->order_reference])}}">{{route('showOrderDetails', ['order_reference' => $attendee->order->order_reference])}}</a>.

@stop
