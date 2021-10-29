<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Country::class, function (Faker $faker) {
    return [
        'capital' => $faker->word,
        'citizenship' => $faker->word,
        'country_code' => $faker->word,
        'currency' => $faker->word,
        'currency_code' => $faker->word,
        'currency_sub_unit' => $faker->word,
        'full_name' => $faker->word,
        'iso_3166_2' => $faker->word,
        'iso_3166_3' => $faker->word,
        'name' => $faker->name,
        'region_code' => $faker->word,
        'sub_region_code' => $faker->word,
        'eea' => $faker->boolean,
    ];
});
