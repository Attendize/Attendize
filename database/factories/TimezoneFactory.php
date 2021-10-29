<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Timezone::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'location' => $faker->word,
    ];
});
