@extends('en.Emails.Layouts.Master')

@section('message_content')

    <p>Привет</p>
    <p>
        Вы получили возмещение за аннулированный билет на мероприятие <b>{{{$attendee->event->title}}}</b>.
        <b>{{{ $refund_amount }}} был возвращен первоначальному получателю платежа, вы должны увидеть платеж через несколько дней.</b>
    </p>

    <p>
        Вы можете связаться с <b>{{{ $attendee->event->organiser->name }}}</b> напрямую по электронной почте <a href='mailto:{{{$attendee->event->organiser->email}}}'>{{{$attendee->event->organiser->email}}}</a> или ответив на это письмо, если вам нужна дополнительная информация.
    </p>
@stop

@section('footer')

@stop