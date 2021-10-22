<?php

use App\Models\PaymentGateway;

$factory->define(PaymentGateway::class, function (Faker\Generator $faker) {
    return [
        'provider_name' => $faker->company,
        'provider_url' => $faker->url,
        'is_on_site' => $faker->boolean(),
        'can_refund' => $faker->boolean(),
        'name' => $faker->company,
        'default' => $faker->boolean(),
        'admin_blade_template' => '',
        'checkout_blade_template' => '',
    ];
});

$factory->state(PaymentGateway::class, 'Dummy', [
    'provider_name' => 'Dummy/Test Gateway',
    'provider_url' => 'none',
    'is_on_site' => 1,
    'can_refund' => 1,
    'name' => 'Dummy',
    'default' => 1,
    'admin_blade_template' => '',
    'checkout_blade_template' => 'Public.ViewEvent.Partials.Dummy',
]);

$factory->state(PaymentGateway::class, 'Stripe', [
    'name' => 'Stripe',
    'provider_name' => 'Stripe',
    'provider_url' => 'https://www.stripe.com',
    'is_on_site' => 1,
    'can_refund' => 1,
    'default' => 0,
    'admin_blade_template' => 'ManageAccount.Partials.Stripe',
    'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentStripe',
]);

$factory->state(PaymentGateway::class, 'Stripe SCA', [
    'provider_name' => 'Stripe SCA',
    'provider_url' => 'https://www.stripe.com',
    'is_on_site' => 0,
    'can_refund' => 1,
    'name' => 'Stripe\PaymentIntents',
    'default' => 0,
    'admin_blade_template' => 'ManageAccount.Partials.StripeSCA',
    'checkout_blade_template' => 'Public.ViewEvent.Partials.PaymentStripeSCA',
]);
