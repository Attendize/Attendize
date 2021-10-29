<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Message::class, function (Faker $faker) {
    return [
        'message' => $faker->text,
        'subject' => $faker->word,
        'recipients' => $faker->word,
        'account_id' => $faker->randomNumber(),
        'user_id' => $faker->randomNumber(),
        'event_id' => factory(App\Models\Event::class),
        'is_sent' => $faker->randomNumber(),
        'sent_at' => $faker->dateTime(),
    ];
});
