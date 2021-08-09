@extends('en.Emails.Layouts.Master')

@section('message_content')

    <p>Hey,</p>
    <p>
        Du hast eine Rückerstattung für dein Ticket bei <b>{{{$attendee->event->title}}}</b> erhalten.
        <b>{{{ $refund_amount }}} wurde dem ursprünglichen Zahlungsberechtigten gutgeschrieben und sollte bald eintreffen.</b>
    </p>

    <p>
        Solltest Du mehr Informationen brauchen kannst Du <b>{{{$attendee->event->organiser->name}}}</b> gleich hier <a href='mailto:{{{$attendee->event->organiser->email}}}'>{{{$attendee->event->organiser->email}}}</a> kontaktieren oder dieser E-Mail antworten.
    </p>
@stop

@section('footer')

@stop
