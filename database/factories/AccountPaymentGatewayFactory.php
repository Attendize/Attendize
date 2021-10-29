<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\AccountPaymentGateway::class, function (Faker $faker) {
    return [
        'account_id' => factory(App\Models\Account::class),
        'payment_gateway_id' => factory(App\Models\PaymentGateway::class),
        'config' => $faker->text,
    ];
});
