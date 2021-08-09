@extends('en.Emails.Layouts.Master')

@section('message_content')
    <div>
        Привет,<br><br>
        Чтобы сбросить пароль, нажмите на <a href='{{ route('password.reset', ['token' => $token]) }}'>эту ссылку</a>.
        <br><br><br>
        Спасибо,<br>
        Команда Attendize
    </div>
@stop