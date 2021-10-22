<?php

$factory->define(App\Models\DateTimeFormat::class, function (Faker\Generator $faker) {
    return [
        'format' => 'Y-m-d H:i:s',
        'picker_format' => 'Y-m-d H:i:s',
        'label' => 'utc',
    ];
});
