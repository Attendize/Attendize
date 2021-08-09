@extends('en.Emails.Layouts.Master')

@section('message_content')

<p>Привет</p>
<p>
    Для вас был создан аккаунт в приложении  {{ config('attendize.app_name') }} участником {{$inviter->first_name.' '.$inviter->last_name}}.
</p>

<p>
    Вы можете войти в систему, используя следующие данные.<br><br>
    
    Имя пользователя: <b>{{$user->email}}</b> <br>
    Пароль: <b>{{$temp_password}}</b>
</p>

<p>
    Вы можете изменить свой временный пароль после входа в систему.
</p>

<div style="padding: 5px; border: 1px solid #ccc;" >
   {{route('login')}}
</div>
<br><br>
<p>
    Если у вас есть какие-либо вопросы, пожалуйста, ответьте на это письмо.
</p>
<p>
    Спасибо
</p>

@stop

@section('footer')


@stop
