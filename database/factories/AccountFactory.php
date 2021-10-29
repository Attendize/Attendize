<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Account::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->safeEmail,
        'timezone_id' => $faker->randomNumber(),
        'date_format_id' => $faker->randomNumber(),
        'datetime_format_id' => $faker->randomNumber(),
        'currency_id' => factory(App\Models\Currency::class),
        'name' => $faker->name,
        'last_ip' => $faker->word,
        'last_login_date' => $faker->dateTime(),
        'address1' => $faker->streetAddress,
        'address2' => $faker->secondaryAddress,
        'city' => $faker->city,
        'state' => $faker->word,
        'postal_code' => $faker->postcode,
        'country_id' => $faker->randomNumber(),
        'email_footer' => $faker->text,
        'is_active' => $faker->boolean,
        'is_banned' => $faker->boolean,
        'is_beta' => $faker->boolean,
        'stripe_access_token' => $faker->word,
        'stripe_refresh_token' => $faker->word,
        'stripe_secret_key' => $faker->word,
        'stripe_publishable_key' => $faker->word,
        'stripe_data_raw' => $faker->text,
        'payment_gateway_id' => $faker->randomNumber(),
    ];
});
