<?php

use Carbon\Carbon;

$factory->define(App\Models\Event::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->name,
        'location' => $faker->text,
        'bg_type' => 'color',
        'bg_color' => config('attendize.event_default_bg_color'),
        'bg_image_path' => $faker->imageUrl,
        'description' => $faker->text,
        'start_date' => Carbon::now()->format(config('attendize.default_datetime_format')),
        'end_date' => Carbon::now()->addWeek()->format(config('attendize.default_datetime_format')),
        'on_sale_date' => Carbon::now()->subDays(20)->format(config('attendize.default_datetime_format')),
        'account_id' => function () {
            return factory(App\Models\Account::class)->create()->id;
        },
        'user_id' => function () {
            return factory(App\Models\User::class)->create()->id;
        },
        'currency_id' => function () {
            return factory(App\Models\Currency::class)->create()->id;
        },
        'organiser_fee_fixed' => 0.00,
        'organiser_fee_percentage' => 0.00,
        'organiser_id' => function () {
            return factory(App\Models\Organiser::class)->create()->id;
        },
        'venue_name' => $faker->name,
        'venue_name_full' => $faker->name,
        'location_address' => $faker->address,
        'location_address_line_1' => $faker->streetAddress,
        'location_address_line_2' => $faker->secondaryAddress,
        'location_country' => $faker->country,
        'location_country_code' => $faker->countryCode,
        'location_state' => $faker->state,
        'location_post_code' => $faker->postcode,
        'location_street_number' => $faker->buildingNumber,
        'location_lat' => $faker->latitude,
        'location_long' => $faker->longitude,
        'location_google_place_id' => $faker->randomDigit,
        'pre_order_display_message' => $faker->text,
        'post_order_display_message' => $faker->text,
        'social_share_text' => 'Check Out [event_title] - [event_url]',
        'social_show_facebook' => true,
        'social_show_linkedin' => true,
        'social_show_twitter' => true,
        'social_show_email' => true,
        'social_show_googleplus' => true,
        'location_is_manual' => 0,
        'is_live' => false,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
        'deleted_at' => null,
    ];
});
