<?php namespace App\Models;

use File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PDF;
use Illuminate\Support\Str;
use Superbalist\Money\Money;

class Order extends MyBaseModel
{
    use SoftDeletes;

    /**
     * The validation rules of the model.
     *
     * @var array $rules
     */
    public $rules = [
        'order_first_name' => ['required'],
        'order_last_name'  => ['required'],
        'order_email' => ['required', 'email'],
    ];

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'order_status_id',
        'amount',
        'account_id',
        'event_id',
        'taxamt',
    ];

    /**
     * The validation error messages.
     *
     * @var array $messages
     */
    public $messages = [
        'order_first_name.required' => 'Please enter a valid first name',
        'order_last_name.required'  => 'Please enter a valid last name',
        'order_email.email' => 'Please enter a valid email',
    ];

    protected $casts = [
        'is_business' => 'boolean',
        'is_refunded' => 'boolean',
        'is_partially_refunded' => 'boolean',
    ];

    /**
     * The items associated with the order.
     *
     * @return HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * The attendees associated with the order.
     *
     * @return HasMany
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    /**
     * The account associated with the order.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * The event associated with the order.
     *
     * @return BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * The tickets associated with the order.
     * @return BelongsToMany
     */
    public function tickets()
    {
        return $this->belongsToMany(
            Ticket::class,
            'ticket_order',
            'order_id',
            'ticket_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function payment_gateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    /**
     * The status associated with the order.
     *
     * @return BelongsTo
     */
    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }


    /**
     * Get the organizer fee of the order.
     *
     * @return \Illuminate\Support\Collection|mixed|static
     */
    public function getOrganiserAmountAttribute()
    {
        return $this->amount + $this->extras + $this->organiser_booking_fee + $this->taxamt;
    }

    /**
     * Get the total amount of the order.
     *
     * @return \Illuminate\Support\Collection|mixed|static
     */
    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->organiser_booking_fee + $this->booking_fee + $this->extras;
    }

    /**
     * Get the full name of the order.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Generate and save the PDF tickets.
     *
     * @todo Move this from the order model
     *
     * @return bool
     */
    public function generatePdfTickets()
    {
        $data = [
            'order'     => $this,
            'event'     => $this->event,
            'tickets'   => $this->event->tickets,
            'attendees' => $this->attendees,
            'css'       => file_get_contents(public_path('assets/stylesheet/ticket.css')),
            'image'     => base64_encode(file_get_contents(public_path($this->event->organiser->full_logo_path))),
        ];

        $pdf_file_path = public_path(config('attendize.event_pdf_tickets_path')) . '/' . $this->order_reference;
        $pdf_file = $pdf_file_path . '.pdf';

        if (file_exists($pdf_file)) {
            return true;
        }

        if (!is_dir($pdf_file_path)) {
            File::makeDirectory(dirname($pdf_file_path), 0777, true, true);
        }

        PDF::setOutputMode('F'); // force to file
        PDF::html('Public.ViewEvent.Partials.PDFTicket', $data, $pdf_file_path);

        $this->ticket_pdf_path = config('attendize.event_pdf_tickets_path') . '/' . $this->order_reference . '.pdf';
        $this->save();

        return file_exists($pdf_file);
    }

    /**
     * Boot all of the bootable traits on the model.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            do {
                    //generate a random string using Laravel's Str::Random helper
                    $token = Str::Random(5) . date('jn');
            } //check if the token already exists and if it does, try again

			while (Order::where('order_reference', $token)->first());
            $order->order_reference = $token;
		});
    }

    /**
     * @return Money
     */
    public function getOrderAmount()
    {
        // We need to show if an order has been refunded
        if ($this->is_refunded) {
            return $this->getRefundedAmountExcludingTax();
        }

        $orderValue = new Money($this->amount, $this->getEventCurrency());
        $bookingFee = new Money($this->organiser_booking_fee, $this->getEventCurrency());

        return $orderValue->add($bookingFee);
    }

    /**
     * @return Money
     */
    public function getOrderTaxAmount()
    {
        $currency = $this->getEventCurrency();
        $taxAmount = (new Money($this->taxamt, $currency));

        return $taxAmount;
    }

    /**
     * @return Money
     */
    public function getMaxAmountRefundable()
    {
        $currency = $this->getEventCurrency();
        $organiserAmount = new Money($this->organiser_amount, $currency);
        $refundedAmount = new Money($this->amount_refunded, $currency);
        return $organiserAmount->subtract($refundedAmount);
    }

    /**
     * @return Money
     */
    public function getRefundedAmountExcludingTax()
    {
        // Setup the currency on the event for transformation
        $currency = $this->getEventCurrency();
        $taxAmount = (new Money($this->taxamt, $currency));
        $amountRefunded = (new Money($this->amount_refunded, $currency));

        return $amountRefunded->subtract($taxAmount);
    }

    /**
     * @return Money
     */
    public function getRefundedAmountIncludingTax()
    {
        return (new Money($this->amount_refunded, $this->getEventCurrency()));
    }

    /**
     * @return Money
     */
    public function getPartiallyRefundedAmount()
    {
        return (new Money($this->amount_refunded, $this->getEventCurrency()));
    }

    /**
     * @return \Superbalist\Money\Currency
     */
    public function getEventCurrency()
    {
        // Get the event currency
        $eventCurrency = $this->event()->first()->currency()->first();

        // Transform the event currency for use in the Money library
        return new \Superbalist\Money\Currency(
            $eventCurrency->code,
            empty($eventCurrency->symbol_left) ? $eventCurrency->symbol_right : $eventCurrency->symbol_left,
            $eventCurrency->title,
            !empty($eventCurrency->symbol_left)
        );
    }

    /**
     * @return boolean
     */
    public function canRefund()
    {
        // Guard against orders that does not contain a payment gateway, ex: Free tickets
        if (is_null($this->payment_gateway)) {
            return false;
        }

        return $this->payment_gateway->can_refund;
    }

    /**
     * @return Collection
     */
    public function getAllNonCancelledAttendees()
    {
        return $this->attendees()->where('is_cancelled', false)->get();
    }
}
