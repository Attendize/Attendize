@extends('Emails.Layouts.Master')

@section('message_content')

<p>@lang("basic.hello") {{ $attendee->first_name }},</p>

<p>@lang("Email.message_received_from_organiser", ["organiser_name"=>$event->organiser->name, "event_title"=>$event->title])</p>

<p style="padding: 10px; margin:10px; border: 1px solid #f3f3f3;">
    {!! nl2br($content) !!}
</p>

<p>@lang("Email.contact_organiser", ["organiser_name"=>$event->organiser->name, "organiser_email"=>$event->organiser->email])</p>
@stop

@section('footer')

@stop