Привет {{{$attendee->first_name}}},<br><br>

Мы прикрепили ваши билеты к этому письму.<br><br>

Вы можете просмотреть информацию о заказе и скачать билеты на сайте {{route('showOrderDetails', ['order_reference' => $attendee->order->order_reference])}} в любое удобное вам время.<br><br>

Ссылка на ваш заказ <b>{{$attendee->order->order_reference}}</b>.<br>

Спасибо за ваш заказ<br>

