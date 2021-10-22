<?php

use App\Models\Account;
use App\Models\Currency;
use App\Models\DateFormat;
use App\Models\DateTimeFormat;
use App\Models\Timezone;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Support\Str;

$factory->define(Account::class, function (Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'timezone_id' => function () {
            return factory(Timezone::class)->create()->id;
        },
        'date_format_id' => function () {
            return factory(DateFormat::class)->create()->id;
        },
        'datetime_format_id' => function () {
            return factory(DateTimeFormat::class)->create()->id;
        },
        'currency_id' => function () {
            return factory(Currency::class)->create()->id;
        },
        'name' => $faker->name,
        'last_ip' => '127.0.0.1',
        'last_login_date' => Carbon::now()->subDays(2),
        'address1' => $faker->address,
        'address2' => '',
        'city' => $faker->city,
        'state' => $faker->stateAbbr,
        'postal_code' => $faker->postcode,
//        'country_id'             => factory(App\Models\Country::class)->create()->id,
        'email_footer' => 'Email footer text',
        'is_active' => false,
        'is_banned' => false,
        'is_beta' => false,
        'stripe_access_token' => Str::random(10),
        'stripe_refresh_token' => Str::random(10),
        'stripe_secret_key' => Str::random(10),
        'stripe_publishable_key' => Str::random(10),
    ];
});
