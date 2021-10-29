<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\QuestionOption::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'question_id' => factory(App\Models\Question::class),
    ];
});
