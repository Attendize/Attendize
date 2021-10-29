<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'account_id' => factory(App\Models\Account::class),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'phone' => $faker->phoneNumber,
        'email' => $faker->safeEmail,
        'password' => bcrypt($faker->password),
        'confirmation_code' => $faker->word,
        'is_registered' => $faker->boolean,
        'is_confirmed' => $faker->boolean,
        'is_parent' => $faker->boolean,
        'remember_token' => Str::random(10),
        'api_token' => $faker->word,
    ];
});
