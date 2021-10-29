<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Ticket::class, function (Faker $faker) {
    return [
        'edited_by_user_id' => $faker->randomNumber(),
        'account_id' => $faker->randomNumber(),
        'order_id' => $faker->randomNumber(),
        'event_id' => factory(App\Models\Event::class),
        'title' => $faker->word,
        'description' => $faker->text,
        'price' => $faker->randomFloat(),
        'max_per_person' => $faker->randomNumber(),
        'min_per_person' => $faker->randomNumber(),
        'quantity_available' => $faker->randomNumber(),
        'quantity_sold' => $faker->randomNumber(),
        'start_sale_date' => $faker->dateTime(),
        'end_sale_date' => $faker->dateTime(),
        'sales_volume' => $faker->randomFloat(),
        'organiser_fees_volume' => $faker->randomFloat(),
        'is_paused' => $faker->boolean,
        'public_id' => $faker->randomNumber(),
        'user_id' => $faker->randomNumber(),
        'sort_order' => $faker->randomNumber(),
        'is_hidden' => $faker->boolean,
    ];
});
