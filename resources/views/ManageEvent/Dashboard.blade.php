@extends('Shared.Layouts.Master')

@section('title')
    @parent
    @lang("basic.dashboard")
@stop


@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
<i class="ico-home2"></i>
@lang("basic.event_dashboard")
@endsection

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css" integrity="sha256-szHusaozbQctTn4FX+3l5E0A5zoxz7+ne4fr8NgWJlw=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.4/raphael-min.js" integrity="sha256-Gk+dzc4kV2rqAZMkyy3gcfW6Xd66BhGYjVWa/FjPu+s=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js" integrity="sha256-0rg2VtfJo3VUij/UY9X0HJP7NET6tgAY98aMOfwP0P8=" crossorigin="anonymous"></script>
    <script>
        $(function () {
            $.getJSON('https://graph.facebook.com/?id=' + '{{route('showEventPage',['event_id' => $event->id, 'event_slug' => Str::slug($event->title)])}}', function (fbdata) {
                $('#facebook-count').html(fbdata.shares);
            });
        });
    </script>

    <style>
        svg {
            width: 100% !important;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-sm-3">
            <div class="stat-box">
                <h3>{{ $event->getEventRevenueAmount()->display() }}</h3>
                <span>@lang("Event.revenue")</span>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="stat-box">
                <h3>{{ $event->orders->count() }}</h3>
                <span>@lang("Dashboard.orders")</span>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="stat-box">
                <h3>{{ $event->tickets->sum('quantity_sold') }}</h3>
                <span>@lang("Dashboard.tickets_sold")</span>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="stat-box">
                <h3>{{ $event->stats->sum('views') }}</h3>
                <span>@lang("Dashboard.event_views")</span>
            </div>
        </div>

        <!-- May be implemented soon.
        <div class="col-sm-3 hide">
            <div class="stat-box">
                <h3 id="facebook-count">0</h3>
                <span>Facebook Shares</span>
            </div>
        </div>
        -->
    </div>

    <div class="row">
        <div class="col-md-9 col-sm-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-heading panel-default">
                            <h3 class="panel-title">
                                @lang("Dashboard.tickets_sold")
                        <span style="color: green; float: right;">
                            {{$event->tickets->sum('quantity_sold')}} @lang("basic.total")
                        </span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="chart-wrap">
                                <div style="height:200px;" class="statChart" id="theChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-heading panel-default">
                            <h3 class="panel-title">
                                @lang("Dashboard.ticket_sales_volume")
                                <span style="color: green; float: right;">
                                    {{ $event->getEventRevenueAmount()->display() }}
                                    @lang("basic.total")
                                </span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="chart-wrap">
                                <div style="height: 200px;" class="statChart" id="theChart3"></div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-heading panel-default">
                            <h3 class="panel-title">
                                @lang("Dashboard.event_page_visits")
                                <span style="color: green; float: right;">
                                    {{$event->stats->sum('views')}} @lang("basic.total")
                                </span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="chart-wrap">
                                <div style="height: 200px;" class="statChart" id="theChart2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel">
                        <div class="panel-heading panel-default">
                            <h3 class="panel-title">
                                @lang("Dashboard.registrations_by_ticket")
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="chart-wrap">
                                <div style="height:200px;" class="statChart" id="pieChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-3 col-sm-6">
            <div class="panel panel-success ticket">
                <div class="panel-body">
                    <i class="ico ico-clock"></i>
                    @if($event->happening_now)
                        @lang("Dashboard.this_event_is_on_now")
                    @else
                        <span id="countdown"></span>
                    @endif
                </div>
            </div>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ico-link mr5 ellipsis"></i>
                        @lang("Dashboard.event_url")
                    </h3>
                </div>

                <div class="panel-body">
                    {!! Form::input('text', 'front_end_url', $event->event_url, ['class' => 'form-control', 'onclick' => 'this.select();']) !!}
                </div>

            </div>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ico-share mr5 ellipsis"></i>
                        @lang("Dashboard.share_event")
                    </h3>
                </div>

                <div class="panel-body">
                    <ul class="rrssb-buttons clearfix">
                        <li class="rrssb-facebook">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{$event->event_url}}?utm_source=fb"
                               class="popup">
                            <span class="rrssb-icon">
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px"
                                     height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28"
                                     xml:space="preserve">
                                <path d="M27.825,4.783c0-2.427-2.182-4.608-4.608-4.608H4.783c-2.422,0-4.608,2.182-4.608,4.608v18.434
                                      c0,2.427,2.181,4.608,4.608,4.608H14V17.379h-3.379v-4.608H14v-1.795c0-3.089,2.335-5.885,5.192-5.885h3.718v4.608h-3.726
                                      c-0.408,0-0.884,0.492-0.884,1.236v1.836h4.609v4.608h-4.609v10.446h4.916c2.422,0,4.608-2.188,4.608-4.608V4.783z"/>
                                </svg>
                            </span>
                                <span class="rrssb-text">@lang("Social.facebook")</span>
                            </a>
                        </li>
                        <li class="rrssb-linkedin">
                            <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{$event->event_url}}?utm_source=linkedin&amp;title={{urlencode($event->title)}}&amp;summary={{{Str::words(md_to_str($event->description), 20)}}}"
                               class="popup">
                            <span class="rrssb-icon">
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px"
                                     height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28"
                                     xml:space="preserve">
                                <path d="M25.424,15.887v8.447h-4.896v-7.882c0-1.979-0.709-3.331-2.48-3.331c-1.354,0-2.158,0.911-2.514,1.803
                                      c-0.129,0.315-0.162,0.753-0.162,1.194v8.216h-4.899c0,0,0.066-13.349,0-14.731h4.899v2.088c-0.01,0.016-0.023,0.032-0.033,0.048
                                      h0.033V11.69c0.65-1.002,1.812-2.435,4.414-2.435C23.008,9.254,25.424,11.361,25.424,15.887z M5.348,2.501
                                      c-1.676,0-2.772,1.092-2.772,2.539c0,1.421,1.066,2.538,2.717,2.546h0.032c1.709,0,2.771-1.132,2.771-2.546
                                      C8.054,3.593,7.019,2.501,5.343,2.501H5.348z M2.867,24.334h4.897V9.603H2.867V24.334z"/>
                                </svg>
                            </span>
                                <span class="rrssb-text">@lang("Social.linkedin")</span>
                            </a>
                        </li>
                        <li class="rrssb-twitter">
                            <a href="http://twitter.com/intent/tweet?text=Check out: {{$event->event_url}}?utm_source=twitter {{ Str::words(md_to_str($event->description), 20) }}"
                               class="popup">
                            <span class="rrssb-icon">
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     width="28px" height="28px" viewBox="0 0 28 28"
                                     enable-background="new 0 0 28 28" xml:space="preserve">
                                <path d="M24.253,8.756C24.689,17.08,18.297,24.182,9.97,24.62c-3.122,0.162-6.219-0.646-8.861-2.32
                                      c2.703,0.179,5.376-0.648,7.508-2.321c-2.072-0.247-3.818-1.661-4.489-3.638c0.801,0.128,1.62,0.076,2.399-0.155
                                      C4.045,15.72,2.215,13.6,2.115,11.077c0.688,0.275,1.426,0.407,2.168,0.386c-2.135-1.65-2.729-4.621-1.394-6.965
                                      C5.575,7.816,9.54,9.84,13.803,10.071c-0.842-2.739,0.694-5.64,3.434-6.482c2.018-0.623,4.212,0.044,5.546,1.683
                                      c1.186-0.213,2.318-0.662,3.329-1.317c-0.385,1.256-1.247,2.312-2.399,2.942c1.048-0.106,2.069-0.394,3.019-0.851
                                      C26.275,7.229,25.39,8.196,24.253,8.756z"/>
                                </svg>
                            </span>
                                <span class="rrssb-text">@lang("Social.twitter")</span>
                            </a>
                        </li>

                        <li class="rrssb-googleplus">
                            <a href="https://plus.google.com/share?url={{$event->event_url}}?utm_source=googleplus"
                               class="popup">
                            <span class="rrssb-icon">
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px"
                                     height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28"
                                     xml:space="preserve">
                                <g>
                                    <g>
                                        <path d="M14.703,15.854l-1.219-0.948c-0.372-0.308-0.88-0.715-0.88-1.459c0-0.748,0.508-1.223,0.95-1.663
                                      c1.42-1.119,2.839-2.309,2.839-4.817c0-2.58-1.621-3.937-2.399-4.581h2.097l2.202-1.383h-6.67c-1.83,0-4.467,0.433-6.398,2.027
                                      C3.768,4.287,3.059,6.018,3.059,7.576c0,2.634,2.022,5.328,5.604,5.328c0.339,0,0.71-0.033,1.083-0.068
                                      c-0.167,0.408-0.336,0.748-0.336,1.324c0,1.04,0.551,1.685,1.011,2.297c-1.524,0.104-4.37,0.273-6.467,1.562
                                      c-1.998,1.188-2.605,2.916-2.605,4.137c0,2.512,2.358,4.84,7.289,4.84c5.822,0,8.904-3.223,8.904-6.41
                                      c0.008-2.327-1.359-3.489-2.829-4.731H14.703z M10.269,11.951c-2.912,0-4.231-3.765-4.231-6.037c0-0.884,0.168-1.797,0.744-2.511
                                      c0.543-0.679,1.489-1.12,2.372-1.12c2.807,0,4.256,3.798,4.256,6.242c0,0.612-0.067,1.694-0.845,2.478
                                      c-0.537,0.55-1.438,0.948-2.295,0.951V11.951z M10.302,25.609c-3.621,0-5.957-1.732-5.957-4.142c0-2.408,2.165-3.223,2.911-3.492
                                      c1.421-0.479,3.25-0.545,3.555-0.545c0.338,0,0.52,0,0.766,0.034c2.574,1.838,3.706,2.757,3.706,4.479
                                      c-0.002,2.073-1.736,3.665-4.982,3.649L10.302,25.609z"/>
                                        <polygon points="23.254,11.89 23.254,8.521 21.569,8.521 21.569,11.89 18.202,11.89 18.202,13.604 21.569,13.604 21.569,17.004
                                         23.254,17.004 23.254,13.604 26.653,13.604 26.653,11.89      "/>
                                    </g>
                                </g>
                                </svg>
                            </span>
                                <span class="rrssb-text">@lang("Social.g+")</span>
                            </a>
                        </li>



                        <li class="rrssb-email">
                            <a href="mailto:?subject=Check This Out&body={{urlencode($event->event_url)}}?utm_source=email">
                            <span class="rrssb-icon">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px"
                                     width="28px" height="28px" viewBox="0 0 28 28"
                                     enable-background="new 0 0 28 28" xml:space="preserve"><g>
                                        <path d="M20.111 26.147c-2.336 1.051-4.361 1.401-7.125 1.401c-6.462 0-12.146-4.633-12.146-12.265 c0-7.94 5.762-14.833 14.561-14.833c6.853 0 11.8 4.7 11.8 11.252c0 5.684-3.194 9.265-7.399 9.3 c-1.829 0-3.153-0.934-3.347-2.997h-0.077c-1.208 1.986-2.96 2.997-5.023 2.997c-2.532 0-4.361-1.868-4.361-5.062 c0-4.749 3.504-9.071 9.111-9.071c1.713 0 3.7 0.4 4.6 0.973l-1.169 7.203c-0.388 2.298-0.116 3.3 1 3.4 c1.673 0 3.773-2.102 3.773-6.58c0-5.061-3.27-8.994-9.303-8.994c-5.957 0-11.175 4.673-11.175 12.1 c0 6.5 4.2 10.2 10 10.201c1.986 0 4.089-0.43 5.646-1.245L20.111 26.147z M16.646 10.1 c-0.311-0.078-0.701-0.155-1.207-0.155c-2.571 0-4.595 2.53-4.595 5.529c0 1.5 0.7 2.4 1.9 2.4 c1.441 0 2.959-1.828 3.311-4.087L16.646 10.068z"/>
                                    </g></svg>
                            </span>
                                <span class="rrssb-text">@lang("Social.email")</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="panel panel-success hide">

                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ico-link mr5 ellipsis"></i>
                        @lang("Dashboard.quick_links")
                    </h3>
                </div>

                <div class="panel-body">

                    <a href="" class="btn-link btn">
                        @lang("Dashboard.edit_event_page_design") <i class="ico ico-arrow-right3"></i>
                    </a>
                    <a href="" class="btn-link btn">
                        @lang("Dashboard.create_tickets") <i class="ico ico-arrow-right3"></i>
                    </a>
                    <a href="" class="btn-link btn">
                        @lang("Dashboard.website_embed_code") <i class="ico ico-arrow-right3"></i>
                    </a>
                    <a href="" class="btn-link btn">
                        @lang("Dashboard.generate_affiliate_link") <i class="ico ico-arrow-right3"></i>
                    </a>
                    <a href="" class="btn-link btn">
                        @lang("Dashboard.edit_organiser_fees") <i class="ico ico-arrow-right3"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>

        var chartData = {!! $chartData  !!};
        var ticketData = {!! $ticketData  !!};



        new Morris.Donut({
            element: 'pieChart',
            data: ticketData,
        });

        new Morris.Line({
            element: 'theChart3',
            data: chartData,
            xkey: 'date',
            ykeys: ['sales_volume'],
            labels: ["@lang("Dashboard.sales_volume")"],
            xLabels: 'day',
            xLabelAngle: 30,
            yLabelFormat: function (x) {
                return '{!! $event->currency_symbol !!} ' + x;
            },
            xLabelFormat: function (x) {
                return formatDate(x);
            }
        });
        new Morris.Line({
            element: 'theChart2',
            data: chartData,
            xkey: 'date',
            //ykeys: ['views', 'unique_views'],
            //labels: ['Event Page Views', 'Unique views'],
            ykeys: ['views'],
            labels: ["@lang("Dashboard.event_views")"],
            xLabels: 'day',
            xLabelAngle: 30,
            xLabelFormat: function (x) {
                return formatDate(x);
            }
        });
        new Morris.Line({
            element: 'theChart',
            data: chartData,
            xkey: 'date',
            ykeys: ['tickets_sold'],
            labels: ["@lang("Dashboard.tickets_sold")"],
            xLabels: 'day',
            xLabelAngle: 30,
            lineColors: ['#0390b5', '#0066ff'],
            xLabelFormat: function (x) {
                return formatDate(x);
            }
        });
        function formatDate(x) {
            var m_names = <?=json_encode(array_filter(explode("|", trans("basic.months_short")))); ?>;
            var sup = "";
            var curr_date = x.getDate();

            <?php if(Lang::locale()=="en") { ?>
            if (curr_date == 1 || curr_date == 21 || curr_date == 31) {
                sup = "st";
            }
            else if (curr_date == 2 || curr_date == 22) {
                sup = "nd";
            }
            else if (curr_date == 3 || curr_date == 23) {
                sup = "rd";
            }
            else {
                sup = "th";
            }
            <?php } ?>

            return curr_date + sup + ' ' + m_names[x.getMonth() + 1];
        }

        var target_date = new Date("{{$event->start_date->format('M d, Y H:i')}} ").getTime();
        var now = new Date();
        var countdown = document.getElementById("countdown");
        if (target_date > now) {
            var days, hours, minutes, seconds;
            setCountdown();
            setInterval(function () {
                setCountdown();
            }, 30000);
            function setCountdown() {
                var current_date = new Date().getTime();
                var seconds_left = (target_date - current_date) / 1000;
                // do some time calculations
                days = parseInt(seconds_left / 86400);
                seconds_left = seconds_left % 86400;
                hours = parseInt(seconds_left / 3600);
                seconds_left = seconds_left % 3600;
                minutes = parseInt(seconds_left / 60);
                // format countdown string + set tag value
                countdown.innerHTML = (days > 0 ? '<b>' + days + "</b> @lang("basic.days")<b> " : '') + (hours > 0 ? hours + " </b>@lang("basic.hours")<b> " : '') + (minutes > 0 ? minutes + "</b> @lang("basic.minutes")" : '');
            }
        }

    </script>
@stop
