Hi {{{$attendee->first_name}}},<br><br>

We've attached your tickets to this email.<br><br>

You can view your order info and download your tickets at {{public_route('showOrderDetails', ['order_reference' => $attendee->order->order_reference])}} anytime.<br><br>

Your order reference is <b>{{$attendee->order->order_reference}}</b>.<br>

Thank you<br>

