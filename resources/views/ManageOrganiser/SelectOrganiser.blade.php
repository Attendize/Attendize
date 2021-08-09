@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
    @lang("Organiser.select_organiser")
@stop

@section('head')
    <style>
        .modal-header {
            background-color: transparent !important;
            color: #666 !important;
            text-shadow: none !important;;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel">
                <div class="panel-body">
                    <div class="logo">
                        {!!Html::image('assets/images/logo-dark.png')!!}
                    </div>

                    <h5>@lang("Organiser.continue_to"):</h5>
                    <div class="list-group">
                        @if ($organisers)
                            @foreach($organisers as $organiser)
                                <a href="{{route('showOrganiserDashboard', ['organiser_id'=>$organiser->id] )}}"
                                   class="list-group-item">
                                    {{$organiser->name}}
                                </a>
                            @endforeach
                        @endif
                    </div>

                    <div style="margin-top:-15px; padding: 10px; text-align: center;">
                        @lang("Organiser.or_caps")
                    </div>
                    <a style="color: white;" href="{{route('showCreateOrganiser')}}" class="btn btn-block btn-success">@lang("Organiser.create_new_organiser")</a>
                </div>
            </div>
        </div>
    </div>
@stop

