<?php namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property bool is_cancelled
 * @property Order order
 * @property string first_name
 * @property string last_name
 */
class Attendee extends MyBaseModel
{
    use SoftDeletes;

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'event_id',
        'order_id',
        'ticket_id',
        'account_id',
        'reference',
        'has_arrived',
        'arrival_time'
    ];

    protected $casts = [
        'is_refunded'  => 'boolean',
        'is_cancelled' => 'boolean',
    ];

    /**
     * Generate a private reference number for the attendee. Use for checking in the attendee.
     *
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {

            do {
                //generate a random string using Laravel's Str::Random helper
                $token = Str::Random(15);
            } //check if the token already exists and if it does, try again

            while (Attendee::where('private_reference_number', $token)->first());
            $order->private_reference_number = $token;
        });

    }

    /**
     * @param  array  $attendeeIds
     * @return Collection
     */
    public static function findFromSelection(array $attendeeIds = [])
    {
        return (new static)->whereIn('id', $attendeeIds)->get();
    }

    /**
     * The order associated with the attendee.
     *
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The ticket associated with the attendee.
     *
     * @return BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * The event associated with the attendee.
     *
     * @return BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return HasMany
     */
    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    /**
     * Scope a query to return attendees that have not cancelled.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeWithoutCancelled($query)
    {
        return $query->where('attendees.is_cancelled', '=', 0);
    }

    /**
     * Reference index is a number representing the position of
     * an attendee on an order, for example if a given order has 3
     * attendees, each attendee would be assigned an auto-incrementing
     * integer to indicate if they were attendee 1, 2 or 3.
     *
     * The reference attribute is a string containing the order reference
     * and the attendee's reference index.
     *
     * @return string
     */
    public function getReferenceAttribute()
    {
        return $this->order->order_reference . '-' . $this->reference_index;
    }

    /**
     * Get the full name of the attendee.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    /**
     * The attributes that should be mutated to dates.
     *
     * @return array $dates
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'arrival_time'];
    }
}
