<?php

use App\Models\Country;

$factory->define(Country::class, function (Faker\Generator $faker) {
    $countries = json_decode(file_get_contents(database_path().'/seeds/countries.json'), true);
    // Choose a random entry from the countries list
    $country = $faker->randomElement($countries);

    return [
        'capital' => ((isset($country['capital'])) ? $country['capital'] : null),
        'citizenship' => ((isset($country['citizenship'])) ? $country['citizenship'] : null),
        'country_code' => $country['country-code'],
        'currency' => ((isset($country['currency'])) ? $country['currency'] : null),
        'currency_code' => ((isset($country['currency_code'])) ? $country['currency_code'] : null),
        'currency_sub_unit' => ((isset($country['currency_sub_unit'])) ? $country['currency_sub_unit'] : null),
        'full_name' => ((isset($country['full_name'])) ? $country['full_name'] : null),
        'iso_3166_2' => $country['iso_3166_2'],
        'iso_3166_3' => $country['iso_3166_3'],
        'name' => $country['name'],
        'region_code' => $country['region-code'],
        'sub_region_code' => $country['sub-region-code'],
        'eea' => (bool) $country['eea'],
    ];
});

$factory->state(Country::class, 'United Kingdom', [
    'id' => 826,
    'capital' => 'London',
    'citizenship' => 'British',
    'country_code' => '826',
    'currency' => 'pound sterling',
    'currency_code' => 'GBP',
    'currency_sub_unit' => 'penny (pl. pence)',
    'full_name' => 'United Kingdom of Great Britain and Northern Ireland',
    'iso_3166_2' => 'GB',
    'iso_3166_3' => 'GBR',
    'name' => 'United Kingdom',
    'region_code' => '150',
    'sub_region_code' => '154',
    'eea' => true,
]);
