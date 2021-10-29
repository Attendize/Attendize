<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\EventAccessCodes::class, function (Faker $faker) {
    return [
        'event_id' => factory(App\Models\Event::class),
        'code' => $faker->word,
        'usage_count' => $faker->randomNumber(),
    ];
});
