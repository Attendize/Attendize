<div class="col-3">
    <article class="u-block-hover">
        <div class="g-bg-cover">
            <img class="d-flex align-items-end" src="{{asset($event->image_url ?? '#')}}">
        </div>
        <div class="u-block-hover__additional--partially-slide-up h-100 text-center g-z-index-1 mt-auto" style="background-image: url({{asset('assets/images/overlay/1.svg')}})">
            <div class="overlay-details smalll">
                <h2 class="title">{{$event->title}}</h2>
                <h4 class="date">В кино с {{$event->start_date->formatLocalized('%d %B')}} </h4>
                <div class="overlay-details-bottom-part">
                    <a href="" class="share">
                        <svg class="Shape" viewBox="0 0 30.504 33.893" fill="#ffffff" width="35px">
                            <path id="Shape" d="M 25.41962051391602 23.95637512207031 C 24.13169288635254 23.95637512207031 22.97933578491211 24.46681022644043 22.09812164306641 25.26648902893066 L 10.01532936096191 18.20548248291016 C 10.10006237030029 17.81414985656738 10.16784763336182 17.42281913757324 10.16784763336182 17.01446914672852 C 10.16784763336182 16.60612297058105 10.10006237030029 16.21479225158691 10.01532936096191 15.82345771789551 L 21.96255111694336 8.830510139465332 C 22.87765884399414 9.681234359741211 24.08085250854492 10.20868301391602 25.41962051391602 10.20868301391602 C 28.23272323608398 10.20868301391602 30.50354385375977 7.9287428855896 30.50354385375977 5.104341506958008 C 30.50354385375977 2.2799391746521 28.23272323608398 0 25.41962051391602 0 C 22.60651397705078 0 20.33569526672363 2.2799391746521 20.33569526672363 5.104341506958008 C 20.33569526672363 5.512688636779785 20.40348052978516 5.904021739959717 20.48821449279785 6.29535436630249 L 8.54099178314209 13.28830146789551 C 7.625885963439941 12.43757820129395 6.422690391540527 11.91012954711914 5.083923816680908 11.91012954711914 C 2.270819425582886 11.91012954711914 0 14.19006824493408 0 17.01446914672852 C 0 19.8388729095459 2.270819425582886 22.11881256103516 5.083923816680908 22.11881256103516 C 6.422690391540527 22.11881256103516 7.625885963439941 21.59136390686035 8.54099178314209 20.74064064025879 L 20.60683822631836 27.81865882873535 C 20.5221061706543 28.17596435546875 20.47126579284668 28.5502815246582 20.47126579284668 28.92460250854492 C 20.47126579284668 31.66392707824707 22.69124794006348 33.89282608032227 25.41962051391602 33.89282608032227 C 28.14799308776855 33.89282608032227 30.36797142028809 31.66392707824707 30.36797142028809 28.92460250854492 C 30.36797142028809 26.18527030944824 28.14799308776855 23.95637512207031 25.41962051391602 23.95637512207031 L 25.41962051391602 23.95637512207031 Z">
                            </path>
                        </svg>
                        Share</a>
                    <a href="" class="like">
                        <svg class="Shape_A26_Path_4" viewBox="0 0 34 30" fill="#ffffff" width="35px">
                            <path id="Shape_A26_Path_4" d="M 17 5.624999523162842 C 15.7344446182251 2.377499580383301 12.18900012969971 0 8.5 0 C 3.696555614471436 0 0 3.622499465942383 0 8.437499046325684 C 0 15.05437183380127 7.164555549621582 20.1712474822998 17 30 C 26.8354434967041 20.1712474822998 34 15.05437183380127 34 8.437499046325684 C 34 3.622499465942383 30.30344581604004 0 25.5 0 C 21.80722236633301 0 18.26555633544922 2.377499580383301 17 5.624999523162842 Z">
                            </path>
                        </svg>
                        {{$event->views}} Views</a>
                    <div class="buy-btn-wrap">
                        <a href="{{$event->event_url}}" class="buy-btn">Купить билет</a>
                    </div>
                    @if(!empty($event->starting_ticket))
                        <span class="cost">Цена ot: {{$event->starting_ticket->first()->price ?? 'n/a'}} TMT</span>
                    @endif
                </div>
            </div>
        </div>
    </article>
</div>