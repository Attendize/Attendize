<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Question::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'question_type_id' => factory(App\Models\QuestionType::class),
        'account_id' => $faker->randomNumber(),
        'is_required' => $faker->boolean,
        'sort_order' => $faker->randomNumber(),
        'is_enabled' => $faker->boolean,
    ];
});
