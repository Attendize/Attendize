<section id="kinoteator" class="kinoteator-section container">
    <div class="tab-header d-flex justify-content-between col-12">
        <h2 class="">Кинотеатры</h2>
        <div style="height: 5px; margin-left: 5px; position: absolute; bottom: 10px; width: 100px; background-color: rgba(211,61,51,1)"></div>
        <a class="" href="">Посмотреть все</a>
    </div>
    <div class="tab-ozi col-12">
        @include('Bilettm.Partials.FilterMenu')

        <div class="owl-carousel container row" id="kinoteator-tab1" style="padding: 0 !important; margin: 0">
            <div class="slider-slider">
                <div class="row">
                    <div class="col-6">
                        @include('Bilettm.Partials.CinemaItem',['event'=>$cinema->shift(1),'size'=>'big'])
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-6">
                                @include('Bilettm.Partials.CinemaItem',['event'=>$cinema->shift(1)])
                            </div>
                            <div class="col-6">
                                @include('Bilettm.Partials.CinemaItem',['event'=>$cinema->shift(1)])
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6" style="height: 50px;">
                                @include('Bilettm.Partials.CinemaItem',['event'=>$cinema->shift(1)])
                            </div>
                            <div class="col-6" style="height: 50px;">
                                @include('Bilettm.Partials.CinemaItem',['event'=>$cinema->shift(1)])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($cinema->count()>0)
                <div class="slider-slider">
                    <div class="row">
                        @foreach($cinema as $event)
                            <div class="col-6">
                                @include('Bilettm.Partials.CinemaItem',['event'=>$event])
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>