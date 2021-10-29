<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Attendee::class, function (Faker $faker) {
    return [
        'order_id' => factory(App\Models\Order::class),
        'event_id' => factory(App\Models\Event::class),
        'ticket_id' => factory(App\Models\Ticket::class),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->safeEmail,
        'private_reference_number' => $faker->word,
        'is_cancelled' => $faker->boolean,
        'has_arrived' => $faker->boolean,
        'arrival_time' => $faker->dateTime(),
        'account_id' => $faker->randomNumber(),
        'reference_index' => $faker->randomNumber(),
        'is_refunded' => $faker->boolean,
    ];
});
