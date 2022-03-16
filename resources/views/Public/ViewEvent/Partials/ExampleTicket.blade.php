
@foreach($tickets as $ticket)

    <img alt="Ticket"
         src="{!! $ticket->encode('data-url'); !!}"
    />

    <br>
@endforeach