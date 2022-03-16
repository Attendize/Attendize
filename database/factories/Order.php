<?php

use App\Models\Order;
use Illuminate\Support\Carbon;

$factory->define(Order::class, function (Faker\Generator $faker) {
    return [
        'account_id'            => static function () {
            return factory(App\Models\Account::class)->create()->id;
        },
        'order_status_id'       => static function () {
            return factory(App\Models\OrderStatus::class)->create()->id;
        },
        'first_name'            => $faker->firstName,
        'last_name'             => $faker->lastName,
        'email'                 => $faker->email,
        'ticket_pdf_path'       => '/ticket/pdf/path',
        'order_reference'       => Order::generateToken(),
        'transaction_id'        => $faker->sha1,
        'discount'              => .20,
        'booking_fee'           => .10,
        'organiser_booking_fee' => .10,
        'order_date'            => Carbon::now(),
        'notes'                 => $faker->text,
        'is_deleted'            => 0,
        'is_cancelled'          => 0,
        'is_partially_refunded' => 0,
        'is_refunded'           => 0,
        'amount'                => 20.00,
        'amount_refunded'       => 0,
        'event_id'              => static function () {
            return factory(App\Models\Event::class)->create()->id;
        },
        'payment_gateway_id'    => 1,
        'is_payment_received'   => false,
        'taxamt'                => 0
    ];
});
