<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Order::class, function (Faker $faker) {
    return [
        'account_id' => factory(App\Models\Account::class),
        'order_status_id' => factory(App\Models\OrderStatus::class),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->safeEmail,
        'business_name' => $faker->word,
        'business_tax_number' => $faker->word,
        'business_address_line_one' => $faker->word,
        'business_address_line_two' => $faker->word,
        'business_address_state_province' => $faker->word,
        'business_address_city' => $faker->word,
        'business_address_code' => $faker->word,
        'ticket_pdf_path' => $faker->word,
        'order_reference' => $faker->word,
        'transaction_id' => $faker->word,
        'discount' => $faker->randomFloat(),
        'booking_fee' => $faker->randomFloat(),
        'organiser_booking_fee' => $faker->randomFloat(),
        'order_date' => $faker->date(),
        'notes' => $faker->text,
        'is_deleted' => $faker->boolean,
        'is_cancelled' => $faker->boolean,
        'is_partially_refunded' => $faker->boolean,
        'is_refunded' => $faker->boolean,
        'amount' => $faker->randomFloat(),
        'amount_refunded' => $faker->randomFloat(),
        'event_id' => factory(App\Models\Event::class),
        'payment_gateway_id' => factory(App\Models\PaymentGateway::class),
        'is_payment_received' => $faker->boolean,
        'is_business' => $faker->boolean,
        'taxamt' => $faker->randomFloat(),
        'payment_intent' => $faker->word,
    ];
});
