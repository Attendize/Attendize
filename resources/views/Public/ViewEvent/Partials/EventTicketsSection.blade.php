<section id="tickets" class="container">
    <div class="row">
        <h1 class='section_head'>
            @lang("Public_ViewEvent.tickets")
        </h1>
    </div>

    @if($event->end_date->isPast())
        <div class="alert alert-boring">
            @lang("Public_ViewEvent.event_already", ['started' => trans('Public_ViewEvent.event_already_ended')])
        </div>
    @else

        @if($tickets->count() > 0)

            {!! Form::open(['url' => route('postValidateTickets', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="content">
                        <div class="tickets_table_wrap">
                            <table class="table">
                                <?php
                                $is_free_event = true;
                                ?>
                                @foreach($tickets->where('is_hidden', false) as $ticket)
                                    <tr class="ticket" property="offers" typeof="Offer">
                                        <td>
                                <span class="ticket-title semibold" property="name">
                                    {{$ticket->title}}
                                </span>
                                            <p class="ticket-descripton mb0 text-muted" property="description">
                                                {{$ticket->description}}
                                            </p>
                                        </td>
                                        <td style="width:200px; text-align: right;">
                                            <div class="ticket-pricing" style="margin-right: 20px;">
                                                @if($ticket->is_free)
                                                    @lang("Public_ViewEvent.free")
                                                    <meta property="price" content="0">
                                                @else
                                                    <?php
                                                    $is_free_event = false;
                                                    ?>
                                                    <span title='{{money($ticket->price, $event->currency)}} @lang("Public_ViewEvent.ticket_price") + {{money($ticket->total_booking_fee, $event->currency)}} @lang("Public_ViewEvent.booking_fees")'>{{money($ticket->total_price, $event->currency)}} </span>
                                                    <span class="tax-amount text-muted text-smaller">{{ ($event->organiser->tax_name && $event->organiser->tax_value) ? '(+'.money(($ticket->total_price*($event->organiser->tax_value)/100), $event->currency).' '.$event->organiser->tax_name.')' : '' }}</span>
                                                    <meta property="priceCurrency"
                                                          content="{{ $event->currency->code }}">
                                                    <meta property="price"
                                                          content="{{ number_format($ticket->price, 2, '.', '') }}">
                                                @endif
                                            </div>
                                        </td>
                                        <td style="width:85px;">
                                            @if($ticket->is_paused)

                                                <span class="text-danger">
                                    @lang("Public_ViewEvent.currently_not_on_sale")
                                </span>

                                            @else

                                                @if($ticket->sale_status === config('attendize.ticket_status.sold_out'))
                                                    <span class="text-danger" property="availability"
                                                          content="http://schema.org/SoldOut">
                                    @lang("Public_ViewEvent.sold_out")
                                </span>
                                                @elseif($ticket->sale_status === config('attendize.ticket.status.before_sale_date'))
                                                    <span class="text-danger">
                                    @lang("Public_ViewEvent.sales_have_not_started")
                                </span>
                                                @elseif($ticket->sale_status === config('attendize.ticket_status.after_sale_date'))
                                                    <span class="text-danger">
                                    @lang("Public_ViewEvent.sales_have_ended")
                                </span>
                                                @else
                                                    {!! Form::hidden('tickets[]', $ticket->id) !!}
                                                    <meta property="availability" content="http://schema.org/InStock">
                                                    <select name="ticket_{{$ticket->id}}" class="form-control"
                                                            style="text-align: center">
                                                        @if ($tickets->count() > 1)
                                                            <option value="0">0</option>
                                                        @endif
                                                        @for($i=$ticket->min_per_person; $i<=$ticket->max_per_person; $i++)
                                                            <option value="{{$i}}">{{$i}}</option>
                                                        @endfor
                                                    </select>
                                                @endif

                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($tickets->where('is_hidden', true)->count() > 0)
                                <tr class="has-access-codes" data-url="{{route('postShowHiddenTickets', ['event_id' => $event->id])}}">
                                    <td colspan="3"  style="text-align: left">
                                        @lang("Public_ViewEvent.has_unlock_codes")
                                        <div class="form-group" style="display:inline-block;margin-bottom:0;margin-left:15px;">
                                            {!!  Form::text('unlock_code', null, [
                                            'class' => 'form-control',
                                            'id' => 'unlock_code',
                                            'style' => 'display:inline-block;width:65%;text-transform:uppercase;',
                                            'placeholder' => 'ex: UNLOCKCODE01',
                                        ]) !!}
                                            {!! Form::button(trans("basic.apply"), [
                                                'class' => "btn btn-success",
                                                'id' => 'apply_access_code',
                                                'style' => 'display:inline-block;margin-top:-2px;',
                                                'data-dismiss' => 'modal',
                                            ]) !!}
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" style="text-align: center">
                                        @lang("Public_ViewEvent.below_tickets")
                                    </td>
                                </tr>
                                <tr class="checkout">
                                    <td colspan="3">
                                        @if(!$is_free_event)
                                            <div class="hidden-xs pull-left">
                                                <img class=""
                                                     src="{{asset('assets/images/public/EventPage/credit-card-logos.png')}}"/>
                                                @if($event->enable_offline_payments)

                                                    <div class="help-block" style="font-size: 11px;">
                                                        @lang("Public_ViewEvent.offline_payment_methods_available")
                                                    </div>
                                                @endif
                                            </div>

                                        @endif
                                        {!!Form::submit(trans("Public_ViewEvent.register"), ['class' => 'btn btn-lg btn-primary pull-right'])!!}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::hidden('is_embedded', $is_embedded) !!}
            {!! Form::close() !!}

        @else

            <div class="alert alert-boring">
                @lang("Public_ViewEvent.tickets_are_currently_unavailable")
            </div>

        @endif

    @endif

</section>
