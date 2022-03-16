<!DOCTYPE html>
<html>
<head>
    <!-- Keep this page lean as possible.-->
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{$event->title}}
    </title>
    <style type="text/css">
        body, html {
            width: 100%;
            text-align: center;
        }

        body {
            padding-top: 50px;
        }

        img {
            width: 95%;
            border: 2px dashed #{{$event->ticket_border_color}};
            padding: 10px;
            margin-bottom: 20px;
        }

        a {
            color: #000000 !important;
            text-decoration: none;
            font-weight: bold;
        }

        .bottom_info {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

@php
    $ticket_per_page = 2;
    $current_page_tickets = 0;
@endphp

@foreach($tickets as $ticket)

    <img alt="Ticket"
         src="{!! $ticket->encode('data-url'); !!}"
    />

    @if($current_page_tickets % 2)
        <div class="page-break"></div>
    @endif

    @php
        $current_page_tickets++
    @endphp
@endforeach

<div class="bottom_info">
    {{--Attendize is provided free of charge on the condition the below hyperlink is left in place.--}}
    {{--See https://www.attendize.com/license.html for more information.--}}
    @include('Shared.Partials.PoweredBy')
</div>
</body>
</html>

