<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\EventImage::class, function (Faker $faker) {
    return [
        'image_path' => $faker->word,
        'event_id' => $faker->randomNumber(),
        'account_id' => $faker->randomNumber(),
        'user_id' => $faker->randomNumber(),
    ];
});
