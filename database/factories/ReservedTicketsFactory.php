<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\ReservedTickets::class, function (Faker $faker) {
    return [
        'ticket_id' => $faker->randomNumber(),
        'event_id' => $faker->randomNumber(),
        'quantity_reserved' => $faker->randomNumber(),
        'expires' => $faker->dateTime(),
        'session_id' => $faker->word,
    ];
});
