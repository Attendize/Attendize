<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\OrderItem::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'quantity' => $faker->randomNumber(),
        'unit_price' => $faker->randomFloat(),
        'unit_booking_fee' => $faker->randomFloat(),
        'order_id' => $faker->randomNumber(),
        'deleted_at' => $faker->dateTime(),
    ];
});
