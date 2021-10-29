<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\EventStats::class, function (Faker $faker) {
    return [
        'date' => $faker->date(),
        'views' => $faker->randomNumber(),
        'unique_views' => $faker->randomNumber(),
        'tickets_sold' => $faker->randomNumber(),
        'sales_volume' => $faker->randomFloat(),
        'organiser_fees_volume' => $faker->randomFloat(),
        'event_id' => $faker->randomNumber(),
    ];
});
