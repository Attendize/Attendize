@extends('Bilettm.Layouts.BilettmLayout')

@section('after_styles')
    <link href="{{asset('vendor/gijgo/gijgo.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    {{\DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render('category',$category)}}
    @include("Bilettm.Partials.FilterMenu")

    @yield('inner_content')

    <section id="first-add-wrapper" style="margin: 100px 0;">
        <div class="container">
            <div class="row" style="padding: 0 20px;">
                <a href="" style="width: 100%">
                    <img src="{{asset('assets/images/advs/first.png')}}" style="width: 100%">
                </a>
            </div>
        </div>
    </section>

@endsection
@push('after_styles')
<style type="text/css">
    .red_button{
        color: #ffffff;
        background-color: #d33d33;
        height: fit-content;
        font-size: 20px;
        padding: 12px 60px;
        border-radius: 5px;
        margin-right: 5px;
        transition-property: background-color;
        transition-duration: .2s;
    }
</style>
@endpush
@section('after_scripts')

    <script src="{{asset('vendor/gijgo/gijgo.min.js')}}" type="text/javascript"></script>
    <script>
        $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4',
            icons: {
                rightIcon: 'Дата <i class="fa fa-caret-down"></i>'
            }
        });
    </script>

@endsection