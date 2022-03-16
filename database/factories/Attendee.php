<?php

use Illuminate\Support\Carbon;

$factory->define(App\Models\Attendee::class, function (Faker\Generator $faker) {
    return [
        'order_id'                 => static function () {
            return factory(App\Models\Order::class)->create()->id;
        },
        'event_id'                 => static function () {
            return factory(App\Models\Event::class)->create()->id;
        },
        'ticket_id'                => static function () {
            return factory(App\Models\Ticket::class)->create()->id;
        },
        'first_name'               => $faker->firstName,
        'last_name'                => $faker->lastName,
        'email'                    => $faker->email,
        'reference_index'          => $faker->numberBetween(),
        'private_reference_number' => 1,
        'is_cancelled'             => false,
        'is_refunded'              => false,
        'has_arrived'              => false,
        'arrival_time'             => Carbon::now(),
        'account_id'               => static function () {
            return factory(App\Models\Account::class)->create()->id;
        },
    ];
});
