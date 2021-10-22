<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAccessCodes extends MyBaseModel
{
    use SoftDeletes;

    /**
     * @param int $event_id
     * @param string $accessCode
     * @return void
     */
    public static function logUsage($event_id, $accessCode)
    {
        (new static)::where('event_id', $event_id)
            ->where('code', $accessCode)
            ->increment('usage_count');
    }

    /**
     * @param $code
     * @param $event_id
     * @return Collection
     */
    public static function findFromCode($code, $event_id)
    {
        return (new static())
            ->where('code', $code)
            ->where('event_id', $event_id)
            ->get();
    }

    /**
     * The validation rules.
     *
     * @return array $rules
     */
    public function rules()
    {
        return [
            'code' => 'required|string',
        ];
    }

    /**
     * The Event associated with the event access code.
     *
     * @return BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function tickets()
    {
        return $this->belongsToMany(
            Ticket::class,
            'ticket_event_access_code',
            'event_access_code_id',
            'ticket_id'
        )->withTimestamps();
    }
}
