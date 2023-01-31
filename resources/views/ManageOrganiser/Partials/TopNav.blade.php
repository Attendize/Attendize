<ul class="nav navbar-nav navbar-left">
    <!-- Show Side Menu -->
    <li class="navbar-main">
        <a href="javascript:void(0);" class="toggleSidebar" title="Show sidebar">
            <span class="toggleMenuIcon">
                <span class="icon ico-menu"></span>
            </span>
        </a>
    </li>
    <!--/ Show Side Menu -->
    <li class="nav-button ">
        <a target="_blank" href="{{ public_route('showOrganiserHome',[$organiser->id]) }}">
            <span>
                <i class="ico-eye2"></i>&nbsp;@lang("Organiser.organiser_page")
            </span>
        </a>
    </li>
</ul>
