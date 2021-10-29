<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\PaymentGateway::class, function (Faker $faker) {
    return [
        'provider_name' => $faker->word,
        'provider_url' => $faker->word,
        'is_on_site' => $faker->boolean,
        'can_refund' => $faker->boolean,
        'name' => $faker->name,
        'default' => $faker->boolean,
        'admin_blade_template' => $faker->word,
        'checkout_blade_template' => $faker->word,
    ];
});
