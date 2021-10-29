<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Affiliate::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'visits' => $faker->randomNumber(),
        'tickets_sold' => $faker->randomNumber(),
        'sales_volume' => $faker->randomFloat(),
        'last_visit' => $faker->dateTime(),
        'account_id' => $faker->randomNumber(),
        'event_id' => $faker->randomNumber(),
    ];
});
