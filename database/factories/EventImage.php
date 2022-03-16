<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EventImage;
use Faker\Generator as Faker;

$factory->define(EventImage::class, static function (Faker $faker) {
    return [
        'image_path' => $faker->image(
            public_path(config('attendize.event_images_path')), $width = 1360, $height = 635
        ),
        'event_id'   => static function () {
            return factory(App\Models\Event::class)->create()->id;
        },
        'account_id' => static function () {
            return factory(App\Models\Account::class)->create()->id;
        },
        'user_id'    => static function () {
            return factory(App\Models\User::class)->create()->id;
        },
    ];
});
