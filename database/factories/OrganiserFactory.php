<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Organiser::class, function (Faker $faker) {
    return [
        'deleted_at' => $faker->dateTime(),
        'account_id' => factory(App\Models\Account::class),
        'name' => $faker->name,
        'about' => $faker->text,
        'email' => $faker->safeEmail,
        'phone' => $faker->phoneNumber,
        'confirmation_key' => $faker->word,
        'facebook' => $faker->word,
        'twitter' => $faker->word,
        'logo_path' => $faker->word,
        'is_email_confirmed' => $faker->boolean,
        'show_twitter_widget' => $faker->boolean,
        'show_facebook_widget' => $faker->boolean,
        'page_header_bg_color' => $faker->word,
        'page_bg_color' => $faker->word,
        'page_text_color' => $faker->word,
        'enable_organiser_page' => $faker->boolean,
        'google_analytics_code' => $faker->word,
        'google_tag_manager_code' => $faker->word,
        'tax_name' => $faker->word,
        'tax_value' => $faker->word,
        'tax_id' => $faker->word,
        'charge_tax' => $faker->boolean,
    ];
});
