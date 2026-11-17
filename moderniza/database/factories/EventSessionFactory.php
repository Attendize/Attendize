<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EventSession> */
class EventSessionFactory extends Factory
{
    protected $model = EventSession::class;

    public function definition(): array
    {
        return [
            'title' => 'Sesión '.fake()->words(2, true),
            'starts_at' => '2027-03-12 10:00:00',
            'ends_at' => '2027-03-12 11:00:00',
            'room' => 'Aula '.fake()->numberBetween(1, 9),
            'capacity' => 30,
        ];
    }

    /** Asocia la sesión a un evento (y a su cuenta) desde el servidor. */
    public function forEvent(Event $event): static
    {
        return $this->state(fn () => [])->afterMaking(function (EventSession $session) use ($event) {
            $session->event_id = $event->id;
            $session->account_id = $event->account_id;
        });
    }
}
