@extends('en.Emails.Layouts.Master')

@section('message_content')

<p>Привет</p>
<p>
    Ваш билет на мероприяние <b>{{{$attendee->event->title}}}</b> был отменен.
</p>

<p>
    Вы можете связаться с <b>{{{$attendee->event->organiser->name}}}</b> напрямую по электронной почте <a href='mailto:{{{$attendee->event->organiser->email}}}'>{{{$attendee->event->organiser->email}}}</a> или ответив на это письмо, если вам нужна дополнительная информация.
</p>
@stop

@section('footer')

@stop
