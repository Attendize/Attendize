<?php
$is_free_event = true;
?>
@foreach($tickets as $ticket)
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
