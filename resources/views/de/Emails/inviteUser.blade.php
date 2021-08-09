@extends('en.Emails.Layouts.Master')

@section('message_content')

<p>Hallo,</p>
<p>
    Du wurdest zu einem {{ config('attendize.app_name') }} Account von {{$inviter->first_name.' '.$inviter->last_name}} hinzugefügt.
</p>

<p>
    So kannst Du dich anmelden:<br><br>

    Benutzername: <b>{{$user->email}}</b> <br>
    Passwort: <b>{{$temp_password}}</b>
</p>

<p>
    Du kannst das temporäre Passwort ändern, sobald Du angemeldet bist.
</p>

<div style="padding: 5px; border: 1px solid #ccc;" >
   {{route('login')}}
</div>
<br><br>
<p>
    Wenn Du Fragen hast, dann antworte einfach dieser E-Mail.
</p>
<p>
    Danke.
</p>

@stop

@section('footer')


@stop
