<aside class="sidebar sidebar-left sidebar-menu">
    <section class="content">
        @role('attendee check in')
        <h5 class="heading">@lang("Attendee.check_in_menu")</h5>
        @else
            @can('manage organisers')
            <h5 class="heading">@lang("Organiser.organiser_menu")</h5>
            @else
            <h5 class="heading">@lang("Event.event_menu")</h5>
            @endcan
        @endrole

        <ul id="nav" class="topmenu">
            @role('attendee check in')
                <li class="{{ Request::is('*events*') ? 'active' : '' }}">
                    <a href="{{route('index')}}">
                        <span class="figure"><i class="ico-calendar"></i></span>
                        <span class="text">@lang("Organiser.event")</span>
                    </a>
                </li>
            @else
                <li class="{{ Request::is('*dashboard*') ? 'active' : '' }}">
                    <a href="{{route('showOrganiserDashboard', array('organiser_id' => $organiser->id))}}">
                        <span class="figure"><i class="ico-home2"></i></span>
                        <span class="text">@lang("Organiser.dashboard")</span>
                    </a>
                </li>
                @can('manage organisers')
                <li class="{{ Request::is('*events*') ? 'active' : '' }}">
                    <a href="{{route('showOrganiserEvents', array('organiser_id' => $organiser->id))}}">
                        <span class="figure"><i class="ico-calendar"></i></span>
                        <span class="text">@lang("Organiser.event")</span>
                    </a>
                </li>
                <li class="{{ Request::is('*customize*') ? 'active' : '' }}">
                    <a href="{{route('showOrganiserCustomize', array('organiser_id' => $organiser->id))}}">
                        <span class="figure"><i class="ico-cog"></i></span>
                        <span class="text">@lang("Organiser.customize")</span>
                    </a>
                </li>
                @endcan
            @endrole
        </ul>
    </section>
</aside>
