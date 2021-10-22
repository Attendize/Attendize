<?php

use App\Models\Currency;

$factory->define(Currency::class, function (Faker\Generator $faker) {
    return [
        'title' => 'Dollar',
        'symbol_left' => '$',
        'symbol_right' => '',
        'code' => 'USD',
        'decimal_place' => 2,
        'value' => 100.00,
        'decimal_point' => '.',
        'thousand_point' => ',',
        'status' => 1,
    ];
});

$factory->state(Currency::class, 'GBP', [
    'title' => 'Pound Sterling',
    'symbol_left' => '£',
    'symbol_right' => '',
    'code' => 'GBP',
    'decimal_place' => 2,
    'value' => 0.62220001,
    'decimal_point' => '.',
    'thousand_point' => ',',
    'status' => 1,
]);
