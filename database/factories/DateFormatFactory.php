<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\DateFormat::class, function (Faker $faker) {
    return [
        'format' => $faker->word,
        'picker_format' => $faker->word,
        'label' => $faker->word,
    ];
});
