<nav>
    <div class="container">
        <ul class="nav u-nav-v1-1 g-mb-20 category-filter" data-btn-classes="btn btn-md btn-block rounded-0 u-btn-outline-lightgray g-mb-20">

            <li class="nav-item">
                <a class="nav-link active" href="{{$category->customUrl(['sort'=>'popular','start'=>$start,'end'=>$end])}}">Популярное</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{$category->customUrl(['sort'=>'new','start'=>$start,'end'=>$end])}}">Новые</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="tab">Filter <i class="fa fa-caret-down"></i></a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item nav-link"
                       href="{{$category->customUrl(['sort'=>$sort,'start'=>Carbon::now(),'end'=>Carbon::today()->endOfDay()])}}" >Today</a>
                    <a class="dropdown-item nav-link"
                       href="{{$category->customUrl(['sort'=>$sort,'start'=>Carbon::tomorrow(),'end'=>Carbon::tomorrow()->endOfDay()])}}" >Tomorrow</a>
                    <a class="dropdown-item nav-link"
                       href="{{$category->customUrl(['sort'=>$sort,'start'=>Carbon::now(),'end'=>Carbon::today()->endOfWeek()])}}" >This week</a>
                    <a class="dropdown-item nav-link"
                       href="{{$category->customUrl(['sort'=>$sort,'start'=>Carbon::now(),'end'=>Carbon::today()->endOfMonth()])}}" >This month</a>
                </div>
            </li>
            <li class="nav-item dropdown" style="position: relative; margin-left: -20px;">
                <form action="{{$category->url}}" method="post" class="calendar-form">
                    @csrf
                    {{--<a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="tab">Дата <i class="fa fa-caret-down"></i></a>--}}
                    <input id="datepicker" placeholder="Select date" name="date"/>
                </form>
            </li>
        </ul>
    </div>
</nav>