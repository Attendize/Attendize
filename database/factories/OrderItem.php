<?php

$factory->define(App\Models\OrderItem::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->title,
        'quantity' => 5,
        'unit_price' => 20.00,
        'unit_booking_fee' => 2.00,
        'order_id' => function () {
            return factory(App\Models\Order::class)->create()->id;
        },
    ];
});
