<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Event> */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 month', '+3 months');

        return [
            'account_id' => Account::factory(),
            'title' => 'TechFest '.fake()->unique()->numberBetween(1, 9999),
            'description' => 'Evento ficticio de pruebas',
            'start_date' => $start,
            'end_date' => (clone $start)->modify('+2 days'),
            'is_live' => true,
        ];
    }
}
