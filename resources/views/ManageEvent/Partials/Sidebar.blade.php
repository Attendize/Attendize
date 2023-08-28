<aside class="sidebar sidebar-left sidebar-menu">
    <section class="content">
        <h5 class="heading">@lang("basic.main_menu")</h5>
        <ul id="nav_main" class="topmenu">
            <li>
                <a href="{{route('showOrganiserDashboard', ['organiser_id' => $event->organiser->id])}}">
                    <span class="figure"><i class="ico-arrow-left"></i></span>
                    <span class="text">@lang("basic.back_to_page", ["page"=>$event->organiser->name])</span>
                </a>
            </li>
        </ul>
        <h5 class="heading">@lang('basic.event_menu')</h5>
        <ul id="nav_event" class="topmenu">
            <li class="{{ Request::is('*dashboard*') ? 'active' : '' }}">
                <a href="{{route('showEventDashboard', array('event' => $event))}}">
                    <span class="figure"><i class="ico-home2"></i></span>
                    <span class="text">@lang("basic.dashboard")</span>
                </a>
            </li>
            <li class="{{ Request::is('*tickets*') ? 'active' : '' }}">
                <a href="{{route('showEventTickets', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-ticket"></i></span>
                    <span class="text">@lang("basic.tickets")</span>
                </a>
            </li>
            <li class="{{ Request::is('*orders*') ? 'active' : '' }}">
                <a href="{{route('showEventOrders', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-cart"></i></span>
                    <span class="text">@lang("basic.orders")</span>
                </a>
            </li>
            <li class="{{ Request::is('*attendees*') ? 'active' : '' }}">
                <a href="{{route('showEventAttendees', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-user"></i></span>
                    <span class="text">@lang("basic.attendees")</span>
                </a>
            </li>
            <li class="{{ Request::is('*promote*') ? 'active' : '' }} hide">
                <a href="{{route('showEventPromote', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-bullhorn"></i></span>
                    <span class="text">@lang("basic.promote")</span>
                </a>
            </li>
            <li class="{{ Request::is('*customize*') ? 'active' : '' }}">
                <a href="{{route('showEventCustomize', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-cog"></i></span>
                    <span class="text">@lang("basic.customize")</span>
                </a>
            </li>
        </ul>
        <h5 class="heading">@lang("ManageEvent.event_tools")</h5>
        <ul id="nav_event" class="topmenu">
            <li class="{{ Request::is('*check_in*') ? 'active' : '' }}">
                <a href="{{route('showCheckIn', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-checkbox-checked"></i></span>
                    <span class="text">@lang("ManageEvent.check-in")</span>
                </a>
            </li>
            <li class="{{ Request::is('*surveys*') ? 'active' : '' }}">
                <a href="{{route('showEventSurveys', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-question"></i></span>
                    <span class="text">@lang("ManageEvent.surveys")</span>
                </a>
            </li>
            <li class="{{ Request::is('*widgets*') ? 'active' : '' }}">
                <a href="{{route('showEventWidgets', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-code"></i></span>
                    <span class="text">@lang("ManageEvent.widgets")</span>
                </a>
            </li>
            <li class="{{ Request::is('*access_codes*') ? 'active' : '' }}">
                <a href="{{ route('showEventAccessCodes', [ 'event_id' => $event->id ]) }}">
                    <span class="figure"><i class="ico-money"></i></span>
                    <span class="text">@lang("AccessCodes.title")</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
