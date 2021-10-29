<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Currency::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'symbol_left' => $faker->word,
        'symbol_right' => $faker->word,
        'code' => $faker->word,
        'decimal_place' => $faker->randomNumber(),
        'value' => $faker->randomFloat(),
        'decimal_point' => $faker->word,
        'thousand_point' => $faker->word,
        'status' => $faker->randomNumber(),
        'event_id' => factory(App\Models\Event::class),
    ];
});
