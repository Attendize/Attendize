<?php

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'account_id' => function () {
            return factory(App\Models\Account::class)->create()->id;
        },
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'phone' => $faker->phoneNumber,
        'email' => $faker->email,
        'password' => $faker->password,
        'confirmation_code' => $faker->randomNumber,
        'is_registered' => false,
        'is_confirmed' => false,
        'is_parent' => false,
        'remember_token' => $faker->randomNumber,
    ];
});
