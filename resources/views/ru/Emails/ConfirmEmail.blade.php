@extends('en.Emails.Layouts.Master')

@section('message_content')

<p>Привет {{$first_name}}</p>
<p>
    Благодарим вас за регистрацию на {{ config('attendize.app_name') }}. Мы будем рады видеть вас у нас в гостях.
</p>

<p>
    Вы можете создать свое первое мероприятие и подтвердить свою электронную почту, для этого нажмите на <a href='{{route('confirmEmail', ['confirmation_code' => $confirmation_code])}}'>эту ссылку</a>.
</p>

<br><br>
<p>
    Если у вас есть какие-либо вопросы, отзывы или предложения, не стесняйтесь отвечать на это письмо.
</p>
<p>
    Спасибо
</p>

@stop

@section('footer')


@stop
