<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\QuestionAnswer::class, function (Faker $faker) {
    return [
        'attendee_id' => factory(App\Models\Attendee::class),
        'event_id' => $faker->randomNumber(),
        'question_id' => factory(App\Models\Question::class),
        'account_id' => $faker->randomNumber(),
        'answer_text' => $faker->text,
    ];
});
