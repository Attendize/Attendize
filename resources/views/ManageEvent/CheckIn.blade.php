<!doctype html>
<html>
<head>
    <title>
        @lang("Attendee.check_in", ["event"=>$event->title])
    </title>

    {!! Html::script('vendor/vue/dist/vue.min.js') !!}
    {!! Html::script('vendor/vue-resource/dist/vue-resource.min.js') !!}

    {!! Html::style('assets/stylesheet/application.css') !!}
    {!! Html::style('assets/stylesheet/check_in.css') !!}
    {!! Html::script('vendor/jquery/dist/jquery.min.js') !!}

    @include('Shared/Layouts/ViewJavascript')
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        body {
            background: url({{asset('assets/images/background.png')}}) repeat;
            background-color: #2E3254;
            background-attachment: fixed;
        }
    </style>
</head>
<body id="app">
<header>
    <div class="menuToggle hide">
        <i class="ico-menu"></i>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="attendee_input_wrap">
                    <div class="input-group">
                                  <span class="input-group-btn">
                                 <button @click="showQrModal" title="Scan QR Code" class="btn btn-default qr_search" type="button"><i
                                              class="ico-qrcode"></i></button>
                                </span>
                        {!!  Form::text('attendees_q', null, [
                    'class' => 'form-control attendee_search',
                            'id' => 'search',
                            'v-model' => 'searchTerm',
                            '@keyup' => 'fetchAttendees | debounce 500',
                            '@keyup.esc' => 'clearSearch',
                            'placeholder' => trans("ManageEvent.checkin_search_placeholder")
                ])  !!}


                    </div>

                    <span v-if='searchTerm' @click='clearSearch' class="clearSearch ico-cancel"></span>
                </div>
            </div>
        </div>
    </div>
</header>


<section class="attendeeList">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="attendee_list">
                    <h4 class="attendees_title">
                        <span v-if="!searchTerm">
                            @lang("ManageEvent.all_attendees")
                        </span>
                        <span v-else v-cloak>
                            @{{searchResultsCount}} @lang("ManageEvent.result_for") <b>@{{ searchTerm }}</b>
                        </span>
                    </h4>

                    <div style="margin: 10px;" v-if="searchResultsCount == 0 && searchTerm" class="alert alert-info"
                         v-cloak>
                        @lang("ManageEvent.no_attendees_matching") <b>@{{ searchTerm }}</b>
                    </div>

                    <ul v-if="searchResultsCount > 0" class="list-group" id="attendee_list" v-cloak>
                        <li
                        @click="toggleCheckin(attendee)"
                        v-for="attendee in attendees"
                        class="at list-group-item"
                        :class = "{arrived : attendee.has_arrived || attendee.has_arrived == '1'}"
                        >
                            @lang("Attendee.name"): <b>@{{ attendee.first_name }} @{{ attendee.last_name }} </b> &nbsp; <span v-if="!attendee.is_payment_received" class="label label-danger">@lang("Order.awaiting_payment")</span>
                        <br>
                            @lang("Order.reference"): <b>@{{ attendee.order_reference + '-' + attendee.reference_index }}</b>
                        <br>
                            @lang("Order.ticket"): <b>@{{ attendee.ticket }}</b>
                        <a href="" class="ci btn btn-successfulQrRead">
                            <i class="ico-checkmark"></i>
                        </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="hide">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

            </div>
        </div>
    </div>
</footer>

{{--QR Modal--}}
<div role="dialog" id="QrModal" class="scannerModal" v-show="showScannerModal" v-cloak>
    <div class="scannerModalContent">

        <a @click="closeScanner"  class="closeScanner" href="javascript:void(0);">
        <i class="ico-close"></i>
        </a>

        <div class="scannerVideo">
            <div class="ScanResultMessage">
                    <span class="message" v-if="scanResultObject.status == 'error'">
                        @{{ scanResultObject.message }}
                    </span>
                    <span class="message" v-if="scanResultObject.status == 'success'">
                        <span class="fieldName">@lang("Attendee.name")</span><br>
                        <span class="fieldValue">@{{ scanResultObject.name }}</span><br>

                        <span class="fieldName">@lang("Attendee.reference")</span><br> 
                        <span class="fieldValue">@{{scanResultObject.reference }}</span><br>

                        <span class="fieldName">@lang("Attendee.ticket")</span><br>
                        <span class="fieldValue">@{{scanResultObject.ticket }}</span><br>
                    </span>
                    <span v-if="isScanning">
                        <div id="scanning-ellipsis">@lang("Attendee.scanning")<span>.</span><span>.</span><span>.</span></div>
                    </span>
            </div>
            <div v-if="scanResult" id="scannerResult" class="scannerResult @{{ scanResultObject.status }}">
                <i v-if="scanResultObject.status == 'success'" class="ico-checkmark"></i>
                <i v-if="scanResultObject.status == 'error'" class="ico-close"></i>
            </div>
            <video id="videoContainer" playsinline autoplay></video>
        </div>

        <div class="scannerButtons">
                    <a @click="initScanner" v-show="!isScanning" href="javascript:void(0);">
                    @lang("Attendee.scan_another_ticket")
                    </a>
        </div>
    </div>
</div>
{{-- /END QR Modal--}}

<script>
Vue.http.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
</script>

@include("Shared.Partials.LangScript")
<script type="module" src="/assets/javascript/check_in.js"></script>
</body>
</html>
