<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;
use Superbalist\Money\Money;
use URL;

/**
 * @property int start_date
 */
class Event extends MyBaseModel
{
    use SoftDeletes;

    protected $dates = ['start_date', 'end_date', 'on_sale_date'];
    /**
     * The validation error messages.
     *
     * @var array $messages
     */
    protected $messages = [
        'title.required'                       => 'You must at least give a title for your event.',
        'organiser_name.required_without'      => 'Please create an organiser or select an existing organiser.',
        'event_image.mimes'                    => 'Please ensure you are uploading an image (JPG, PNG, JPEG)',
        'event_image.max'                      => 'Please ensure the image is not larger then 3MB',
        'location_venue_name.required_without' => 'Please enter a venue for your event',
        'venue_name_full.required_without'     => 'Please enter a venue for your event',
    ];

    /**
     * The validation rules.
     *
     * @return array $rules
     */
    public function rules()
    {
        $format = config('attendize.default_datetime_format');
        return [
            'title'               => 'required',
            'description'         => 'required',
            'location_venue_name' => 'required_without:venue_name_full',
            'venue_name_full'     => 'required_without:location_venue_name',
            'start_date'          => 'required|date_format:"' . $format . '"',
            'end_date'            => 'required|date_format:"' . $format . '"',
            'organiser_name'      => 'required_without:organiser_id',
            'event_image'         => 'nullable|mimes:jpeg,jpg,png|max:3000',
        ];
    }

    /**
     * The questions associated with the event.
     *
     * @return BelongsToMany
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'event_question');
    }

    /**
     * The questions associated with the event.
     *
     * @return BelongsToMany
     */
    public function questions_with_trashed()
    {
        return $this->belongsToMany(Question::class, 'event_question')->withTrashed();
    }

    /**
     * The images associated with the event.
     *
     * @return HasMany
     */
    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    /**
     * The messages associated with the event.
     *
     * @return mixed
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'DESC');
    }

    /**
     * The tickets associated with the event.
     *
     * @return HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * The affiliates associated with the event.
     *
     * @return HasMany
     */
    public function affiliates()
    {
        return $this->hasMany(Affiliate::class);
    }

    /**
     * The orders associated with the event.
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * The account associated with the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * The organizer associated with the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organiser()
    {
        return $this->belongsTo(Organiser::class);
    }

    /**
     * Get the embed url.
     *
     * @return mixed
     */
    public function getEmbedUrlAttribute()
    {
        return str_replace(['http:', 'https:'], '', route('showEmbeddedEventPage', ['event_id' => $this->id]));
    }

    /**
     * Get the fixed fee.
     *
     * @return mixed
     */
    public function getFixedFeeAttribute()
    {
        return config('attendize.ticket_booking_fee_fixed') + $this->organiser_fee_fixed;
    }

    /**
     * Get the percentage fee.
     *
     * @return mixed
     */
    public function getPercentageFeeAttribute()
    {
        return config('attendize.ticket_booking_fee_percentage') + $this->organiser_fee_percentage;
    }

    /**
     * Parse start_date to a Carbon instance
     *
     * @param  string  $date  DateTime
     */
    public function setStartDateAttribute($date)
    {
        $format = config('attendize.default_datetime_format');

        if ($date instanceof Carbon) {
            $this->attributes['start_date'] = $date->format($format);
        } else {
            $this->attributes['start_date'] = Carbon::createFromFormat($format, $date);
        }
    }

    /**
     * Format start date from user preferences
     * @return String Formatted date
     */
    public function startDateFormatted()
    {
        return $this->start_date->format(config('attendize.default_datetime_format'));
    }

    /**
     * Parse end_date to a Carbon instance
     *
     * @param  string  $date  DateTime
     */
    public function setEndDateAttribute($date)
    {
        $format = config('attendize.default_datetime_format');

        if ($date instanceof Carbon) {
            $this->attributes['end_date'] = $date->format($format);
        } else {
            $this->attributes['end_date'] = Carbon::createFromFormat($format, $date);
        }
    }

    /**
     * Format end date from user preferences
     * @return String Formatted date
     */
    public function endDateFormatted()
    {
        return $this->end_date->format(config('attendize.default_datetime_format'));
    }

    /**
     * Indicates whether the event is currently happening.
     *
     * @return bool
     */
    public function getHappeningNowAttribute()
    {
        return Carbon::now()->between($this->start_date, $this->end_date);
    }

    /**
     * Get the currency symbol.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrencySymbolAttribute()
    {
        return $this->currency->symbol_left;
    }

    /**
     * Get the currency code.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrencyCodeAttribute()
    {
        return $this->currency->code;
    }

    /**
     * Return an array of attendees and answers they gave to questions at checkout
     *
     * @return array
     */
    public function getSurveyAnswersAttribute()
    {
        $rows[] = array_merge([
            'Order Ref',
            'Attendee Name',
            'Attendee Email',
            'Attendee Ticket'
        ], $this->questions->pluck('title')->toArray());

        $attendees = $this->attendees()->has('answers')->get();

        foreach ($attendees as $attendee) {
            $answers = [];

            foreach ($this->questions as $question) {
                if (in_array($question->id, $attendee->answers->pluck('question_id')->toArray())) {
                    $answers[] = $attendee->answers->where('question_id', $question->id)->first()->answer_text;
                } else {
                    $answers[] = null;
                }
            }

            $rows[] = array_merge([
                $attendee->order->order_reference,
                $attendee->full_name,
                $attendee->email,
                $attendee->ticket->title
            ], $answers);
        }

        return $rows;
    }

    /**
     * The attendees associated with the event.
     *
     * @return HasMany
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    /**
     * Get the embed html code.
     *
     * @return string
     */
    public function getEmbedHtmlCodeAttribute()
    {
        return "<!--Attendize.com Ticketing Embed Code-->
                <iframe style='overflow:hidden; min-height: 350px;' frameBorder='0' seamless='seamless' width='100%' height='100%' src='" . $this->embed_url . "' vspace='0' hspace='0' scrolling='auto' allowtransparency='true'></iframe>
                <!--/Attendize.com Ticketing Embed Code-->";
    }

    /**
     * Get a usable address for embedding Google Maps
     *
     */
    public function getMapAddressAttribute()
    {
        $string = $this->venue . ','
            . $this->location_street_number . ','
            . $this->location_address_line_1 . ','
            . $this->location_address_line_2 . ','
            . $this->location_state . ','
            . $this->location_post_code . ','
            . $this->location_country;

        return urlencode($string);
    }

    /**
     * Get the big image url.
     *
     * @return string
     */
    public function getBgImageUrlAttribute()
    {
        return URL::to('/') . '/' . $this->bg_image_path;
    }

    /**
     * Get the sales and fees volume.
     *
     * @return \Illuminate\Support\Collection|mixed|static
     */
    public function getSalesAndFeesVoulmeAttribute()
    {
        return $this->sales_volume + $this->organiser_fees_volume;
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @return array $dates
     */
    public function getDates()
    {
        return ['created_at', 'updated_at', 'start_date', 'end_date'];
    }

    public function getIcsForEvent()
    {
        $siteUrl = URL::to('/');
        $eventUrl = $this->getEventUrlAttribute();
        $description = md_to_str($this->description);
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $timestamp = new Carbon();

        $icsTemplate = <<<ICSTemplate
BEGIN:VCALENDAR
VERSION:2.0
PRODID:{$siteUrl}
BEGIN:VEVENT
UID:{$eventUrl}
DTSTAMP:{$timestamp->format('Ymd\THis\Z')}
DTSTART:{$start_date->format('Ymd\THis\Z')}
DTEND:{$end_date->format('Ymd\THis\Z')}
SUMMARY:$this->title
LOCATION:{$this->venue_name}
DESCRIPTION:{$description}
END:VEVENT
END:VCALENDAR
ICSTemplate;

        return $icsTemplate;
    }

    /**
     * Get the url of the event.
     *
     * @return string
     */
    public function getEventUrlAttribute()
    {
        return route("showEventPage", ["event_id" => $this->id, "event_slug" => Str::slug($this->title)]);
        //return URL::to('/') . '/e/' . $this->id . '/' . Str::slug($this->title);
    }

    /**
     * @param  integer  $accessCodeId
     * @return bool
     */
    public function hasAccessCode($accessCodeId)
    {
        return (is_null($this->access_codes()->where('id', $accessCodeId)->first()) === false);
    }

    /**
     * The access codes associated with the event.
     *
     * @return HasMany
     */
    public function access_codes()
    {
        return $this->hasMany(EventAccessCodes::class, 'event_id', 'id');
    }

    /**
     * @return Money
     */
    public function getEventRevenueAmount()
    {
        $currency = $this->getEventCurrency();

        $eventRevenue = $this->stats()->get()->reduce(function ($eventRevenue, $statsEntry) use ($currency) {
            $salesVolume = (new Money($statsEntry->sales_volume, $currency));
            $organiserFeesVolume = (new Money($statsEntry->organiser_fees_volume, $currency));

            return (new Money($eventRevenue, $currency))->add($salesVolume)->add($organiserFeesVolume);
        });

        return (new Money($eventRevenue, $currency));
    }

    /**
     * @return \Superbalist\Money\Currency
     */
    private function getEventCurrency()
    {
        // Get the event currency
        $eventCurrency = $this->currency()->first();

        // Setup the currency on the event for transformation
        $currency = new \Superbalist\Money\Currency(
            $eventCurrency->code,
            empty($eventCurrency->symbol_left) ? $eventCurrency->symbol_right : $eventCurrency->symbol_left,
            $eventCurrency->title,
            !empty($eventCurrency->symbol_left)
        );
        return $currency;
    }

    /**
     * The currency associated with the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * The stats associated with the event.
     *
     * @return HasMany
     */
    public function stats()
    {
        return $this->hasMany(EventStats::class);
    }

    /**
     * Calculates the event organiser fee from both the fixed and percentage values based on the ticket
     * price
     *
     * return Money
     */
    public function getOrganiserFee(Money $ticketPrice)
    {
        $currency = $this->getEventCurrency();
        $calculatedBookingFee = new Money('0', $currency);

        // Fixed event organiser fees can be added without worry, defaults to zero
        $eventOrganiserFeeFixed = new Money($this->organiser_fee_fixed, $currency);
        $calculatedBookingFee = $calculatedBookingFee->add($eventOrganiserFeeFixed);

        // We have to calculate the event organiser fee percentage from the ticket price
        $eventOrganiserFeePercentage = new Money($this->organiser_fee_percentage, $currency);
        $percentageFeeValue = $ticketPrice->multiply($eventOrganiserFeePercentage)->divide(100);
        $calculatedBookingFee = $calculatedBookingFee->add($percentageFeeValue);

        return $calculatedBookingFee;
    }
}
