<?php

$factory->define(App\Models\Organiser::class, function (Faker\Generator $faker) {
    return [
        'account_id' => function () {
            return factory(App\Models\Account::class)->create()->id;
        },
        'name' => $faker->name,
        'about' => $faker->text,
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
        'facebook' => 'https://facebook.com/organizer-profile',
        'twitter' => 'https://twitter.com/organizer-profile',
        'logo_path' => 'path/to/logo',
        'is_email_confirmed' => 0,
        'confirmation_key' => Str::Random(15),
        'show_twitter_widget' => $faker->boolean,
        'show_facebook_widget' => $faker->boolean,
        'page_header_bg_color' => $faker->hexcolor,
        'page_bg_color' => '#ffffff',
        'page_text_color' => '#000000',
        'enable_organiser_page' => $faker->boolean,
        'google_analytics_code' => null,
        'tax_name' => $faker->text(11).' tax',
        'tax_value' => $faker->randomFloat(2, 0, 30),
        'tax_id' => '',
        'charge_tax' => $faker->boolean,
    ];
});
