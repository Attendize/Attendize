<section id="intro" class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="organiser_logo">
                <div class="thumbnail">
                    <img src="{{URL::to($organiser->full_logo_path)}}" />
                </div>
            </div>
            <h1>{{$organiser->name}}</h1>
            @if($organiser->about)
            <div class="description pa10">
                {!! md_to_html($organiser->about) !!}
            </div>
            @endif
        </div>
    </div>
</section>
