<section id="events" class="container">
    <div class="row">
        <div class="col-xs-12 col-md-8">
            @include('Public.ViewOrganiser.Partials.EventListingPanel',
                [
                    'panel_title' => trans("Public_ViewOrganiser.upcoming_events"),
                    'events'      => $upcoming_events
                ]
            )
            @include('Public.ViewOrganiser.Partials.EventListingPanel',
                [
                    'panel_title' => trans("Public_ViewOrganiser.past_events"),
                    'events'      => $past_events
                ]
            )
        </div>
    </div>
</section>
