<?php

$factory->define(App\Models\DateFormat::class, function (Faker\Generator $faker) {
    return [
        'format'  => 'Y-m-d',
        'picker_format' => 'Y-m-d',
        'label' => 'utc date',
    ];
});
