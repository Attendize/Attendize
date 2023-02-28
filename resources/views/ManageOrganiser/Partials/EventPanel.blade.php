<div class="panel panel-success event">
    <div class="panel-heading" data-style="background-color: {{{$event->bg_color}}};background-image: url({{{$event->bg_image_url}}}); background-size: cover;" {{$event->deleted_at==null?'':'style=background-color:gray;'  }}   >
        <div class="event-date">
            <div class="month">
                {{strtoupper(explode("|", trans("basic.months_short"))[$event->start_date->format('n')])}}
            </div>
            <div class="day">
                {{$event->start_date->format('d')}}
            </div>
        </div>
        <ul class="event-meta">
            <li class="event-title">
                <a title="{{{$event->title}}}" href="{{route('showEventDashboard', ['event_id'=>$event->id])}}">
                    {{{ Str::limit($event->title, $limit = 75, $end = '...') }}}
                </a>
            </li>
            <li class="event-organiser">
                By <a href='{{route('showOrganiserDashboard', ['organiser_id' => $event->organiser->id])}}'>{{{$event->organiser->name}}}</a>
            </li>
        </ul>

    </div>

    <div class="panel-body">
        <ul class="nav nav-section nav-justified mt5 mb5">
            <li>
                <div class="section">
                    <h4 class="nm">{{ $event->tickets->sum('quantity_sold') }}</h4>
                    <p class="nm text-muted">@lang("Event.tickets_sold")</p>
                </div>
            </li>

            <li>
                <div class="section">
                    <h4 class="nm">{{ $event->getEventRevenueAmount()->display() }}</h4>
                    <p class="nm text-muted">@lang("Event.revenue")</p>
                </div>
            </li>
        </ul>
    </div>
    <div class="panel-footer">
        <ul class="nav nav-section nav-justified">
            <li>
                <a href="{{ $event->deleted_at==null? route('showEventCustomize',['event_id' => $event->id]) : '#' }}" {!! $event->deleted_at==null? "" : 'disabled data-toggle'.'="tooltip" title="'.trans("basic.restorebeforeusingthis").'"' !!} >
                    <i class="ico-edit"></i> @lang("basic.edit")
                </a>
            </li>
            <li>
                <a href="{{ $event->deleted_at==null? route('duplicateEvent', ['event_id' => $event->id ] ) : '#' }}" {!! $event->deleted_at==null? "" : 'disabled data-toggle'.'="tooltip" title="'.trans("basic.restorebeforeusingthis").'"' !!} >
                    <i class="ico-copy"></i> @lang("basic.duplicate")
                </a>
            </li>
            <li>
                <a href="{{  $event->deleted_at==null? route('archiveEvent',['event_id' => $event->id]) : route('restoreEvent', ['event_id' => $event->id]) }}">
                    <i class="{{ $event->deleted_at==null?'ico-trash':'ico-undo' }}" > </i> {{ $event->deleted_at==null? '  '.trans("basic.archive") : '  '.trans("basic.restore") }}
                </a>
            </li>
            <li>
                <a href="{{ $event->deleted_at==null? route('showEventDashboard', ['event_id' => $event->id]): '#' }}" {!! $event->deleted_at==null? "" : 'disabled data-toggle'.'="tooltip" title="'.trans("basic.restorebeforeusingthis").'"' !!} >
                    <i class="ico-cog"></i> @lang("basic.manage")
                </a>
            </li>
        </ul>
    </div>
</div>
