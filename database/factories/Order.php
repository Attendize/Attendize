<?php

use Illuminate\Support\Carbon;

$factory->define(App\Models\Order::class, function (Faker\Generator $faker) {
    return [
        'account_id' => function () {
            return factory(App\Models\Account::class)->create()->id;
        },
        'order_status_id' => function () {
            return factory(App\Models\OrderStatus::class)->create()->id;
        },
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'ticket_pdf_path' => '/ticket/pdf/path',
        'order_reference' => $faker->text(15),
        'transaction_id' => $faker->text(50),
        'discount' => .20,
        'booking_fee' => .10,
        'organiser_booking_fee' => .10,
        'order_date' => Carbon::now(),
        'notes' => $faker->text,
        'is_deleted' => 0,
        'is_cancelled' => 0,
        'is_partially_refunded' => 0,
        'is_refunded' => 0,
        'amount' => 20.00,
        'amount_refunded' => 0,
        'event_id' => function () {
            return factory(App\Models\Event::class)->create()->id;
        },
        'payment_gateway_id' => 1,
        'is_payment_received' => false,
        'taxamt' => 0,
    ];
});
