@extends('Shared.Layouts.Master')

@section('title')
@parent

@lang("Event.event_orders")
@stop

@section('top_nav')
@include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
@include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
<i class='ico-cart mr5'></i>
@lang("Event.event_orders")
<span class="page_title_sub_title hide">
    {{ @trans("Event.showing_num_of_orders", [30, \App\Models\Order::scope()->count()]) }}
</span>
@stop

@section('head')

@stop

@section('page_header')
<div class="col-md-9 col-sm-6">
    <!-- Toolbar -->
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group btn-group-responsive">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                <i class="ico-users"></i> @lang("basic.export") <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'xlsx'])}}">@lang("File_format.Excel_xlsx")</a></li>
                <li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'xls'])}}">@lang("File_format.Excel_xls")</a></li>
                <li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'csv'])}}">@lang("File_format.csv")</a></li>
                <li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'html'])}}">@lang("File_format.html")</a></li>
            </ul>
        </div>
    </div>
    <!--/ Toolbar -->
</div>
<div class="col-md-3 col-sm-6">
   {!! Form::open(array('url' => route('showEventOrders', ['event_id'=>$event->id,'sort_by'=>$sort_by]), 'method' => 'get')) !!}
    <div class="input-group">
        <input name='q' value="{{$q or ''}}" placeholder="@lang('Order.search_placeholder')" type="text" class="form-control">
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
    </div>
   {!! Form::close() !!}
</div>
@stop


@section('content')
<!--Start Attendees table-->
<div class="row">

    @if($orders->count())

    <div class="col-md-12">

        <!-- START panel -->
        <div class="panel">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                               {!! Html::sortable_link(trans("Order.order_ref"), $sort_by, 'order_reference', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link(trans("Order.order_date"), $sort_by, 'created_at', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link(trans("Attendee.name"), $sort_by, 'first_name', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link(trans("Attendee.email"), $sort_by, 'email', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link(trans("Order.amount"), $sort_by, 'amount', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link(trans("Order.status"), $sort_by, 'order_status_id', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <a href='javascript:void(0);' data-modal-id='view-order-{{ $order->id }}' data-href="{{route('showManageOrder', ['order_id'=>$order->id])}}" title="@lang("Order.view_order_num", ["num"=>$order->order_reference])" class="loadModal">
                                    {{$order->order_reference}}
                                </a>
                            </td>
                            <td>
                                {{ $order->created_at->format(config('attendize.default_datetime_format')) }}
                            </td>
                            <td>
                                {{$order->first_name.' '.$order->last_name}}
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="loadModal"
                                    data-modal-id="MessageOrder"
                                    data-href="{{route('showMessageOrder', ['event_id' => $event->id, 'order_id'=>$order->id])}}"
                                > {{$order->email}}</a>
                            </td>
                            <td>
                                <span>{{ $order->getOrderAmount()->display() }}</span>
                                <a href="#" class="hint--top" data-hint="{{money($order->amount, $event->currency)}} + {{money($order->organiser_booking_fee, $event->currency)}} @lang("Order.organiser_booking_fees")">
                                    {{money($order->amount + $order->extras + $order->organiser_booking_fee + $order->taxamt, $event->currency)}}
                                @if ($order->is_partially_refunded)
                                    <em>({{ $order->getPartiallyRefundedAmount()->negate()->display() }})</em>
                                @endif
                            </td>
                            <td>
                                <span class="label label-{{(!$order->is_payment_received || $order->is_refunded || $order->is_partially_refunded) ? 'warning' : 'success'}}">
                                    {{$order->orderStatus->name}}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" data-modal-id="cancel-order-{{ $order->id }}" data-href="{{route('showCancelOrder', ['order_id'=>$order->id])}}" title="@lang("Order.cancel_order")" class="btn btn-xs btn-danger loadModal">
                                                @lang("Order.refund/cancel")
                                            </a>
                                <a data-modal-id="view-order-{{ $order->id }}" data-href="{{route('showManageOrder', ['order_id'=>$order->id])}}" title="@lang("Order.view_order")" class="btn btn-xs btn-primary loadModal">@lang("Order.details")</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        {!!$orders->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order, 'q' => $q])->render()!!}
    </div>

    @else

    @if($q)
    @include('Shared.Partials.NoSearchResults')
    @else
    @include('ManageEvent.Partials.OrdersBlankSlate')
    @endif

    @endif
</div>    <!--/End attendees table-->
@stop
