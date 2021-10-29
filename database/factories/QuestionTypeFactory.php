<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\QuestionType::class, function (Faker $faker) {
    return [
        'alias' => $faker->word,
        'name' => $faker->name,
        'has_options' => $faker->boolean,
        'allow_multiple' => $faker->boolean,
    ];
});
