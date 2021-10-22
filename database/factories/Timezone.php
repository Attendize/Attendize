<?php

use App\Models\Timezone;

$factory->define(Timezone::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->timezone,
        'location' => $faker->city,
    ];
});

$factory->state(Timezone::class, 'Europe/London', [
    'name' => 'Europe/London',
    'location' => '(GMT) London',
]);
