@extends('Bilettm.Layouts.BilettmLayout')
@section('content')
    {{\DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render('home')}}
    <section style="margin-top: 30px; margin-bottom: 100px">
        <div class="container">
            <div class="row m-0">
                <div class="col-10">
                    <div class="container" style="padding: 0 !important;">
                        <div class="row">
                            <div class="col-12 p-0" style="margin-bottom: 30px">
                                <h2 class="main-title">«{{$event->title}}»</h2>
                                <div class="main-title-bottom-line"></div>
                            </div>
                            <div class="col-12 p-0" style="padding-left: 10px !important;">
                                @if($event->images->count())
                                    <img style="width: 450px" class="details-image" alt="{{$event->title}}" src="{{config('attendize.cdn_url_user_assets').'/'.$event->images->first()['image_path']}}" property="image">
                                {{--<img src="assets/assets/img/teator/tall6.png" style="width: 450px" class="details-image">--}}
                                @endif
                                    {!! Markdown::parse($event->description) !!}
                                    <b>{{$event->organiser->name}}</b> @lang("Public_ViewEvent.presents")
                                    @lang("Public_ViewEvent.at")
                                    <span property="location" typeof="Place">
                                        <b property="name">{{$event->venue_name}}</b>
                                        <meta property="address" content="{{ urldecode($event->venue_name) }}">
                                    </span>
                            </div>
                            @include('Bilettm.ViewEvent.Partials.TicketSchedule')
                        </div>
                    </div>
                </div>
                <div class="col-2 text-center">

                    @include('Bilettm.ViewEvent.Partials.EventShareButtons')
                    <img src="{{asset('assets/images/advs/adv.png')}}" style="width: 100%">
                </div>
                {{--<div class="col-12 p-0">--}}
                    {{--@include('Bilettm.Partials.EventTags')--}}
                {{--</div>--}}
            </div>
        </div>
    </section>
    <section id="location" class="container p0">
        <div class="row">
            <div class="col-md-12">
                <div class="google-maps content">
                    <iframe frameborder="0" style="border:0;"
                            src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q={{$event->map_address}}&amp;aq=0&amp;oq={{$event->map_address}}&amp;sll=28.659344,-81.187888&amp;sspn=0.128789,0.264187&amp;ie=UTF8&amp;hq={{$event->map_address}}&amp;t=m&amp;z=15&amp;iwloc=A&amp;output=embed">

                    </iframe>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('after_scripts')
    @include("Shared.Partials.LangScript")
    {!!HTML::script(config('attendize.cdn_url_static_assets').'/assets/javascript/frontend.js')!!}

    @if(isset($secondsToExpire))
        <script>if($('#countdown')) {setCountdown($('#countdown'), {{$secondsToExpire}});}</script>
    @endif
@endsection