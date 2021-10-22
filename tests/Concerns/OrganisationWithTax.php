<?php

namespace Tests\Concerns;

use App\Models\Account;
use App\Models\AccountPaymentGateway;
use App\Models\Attendee;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Event;
use App\Models\EventStats;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Organiser;
use App\Models\PaymentGateway;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\Timezone;
use App\Models\User;
use Illuminate\Support\Carbon;
use Superbalist\Money\Money;

trait OrganisationWithTax
{
    private $account;
    private $currency;
    private $paymentGateway;
    private $user;
    private $organiserWithTax;
    private $event;
    private $eventWithPercentageFees;
    private $eventWithFixedFees;

    public function setupOrganisationWithTax()
    {
        $orderStatuses = collect([
            ['id' => config('attendize.order.complete'), 'name' => 'Completed'],
            ['id' => config('attendize.order.refunded'), 'name' => 'Refunded'],
            ['id' => config('attendize.order.partially_refunded'), 'name' => 'Partially Refunded'],
            ['id' => config('attendize.order.cancelled'), 'name' => 'Cancelled'],
        ]);

        $orderStatuses->map(static function ($orderStatus) {
            factory(OrderStatus::class)->create($orderStatus);
        });

        $ticketStatuses = collect([
            ['name' => 'Sold Out'],
            ['name' => 'Sales Have Ended'],
            ['name' => 'Not On Sale Yet'],
            ['name' => 'On Sale'],
        ]);
        $ticketStatuses->map(function ($ticketStatus) {
            factory(TicketStatus::class)->create($ticketStatus);
        });

        $country = factory(Country::class)->states('United Kingdom')->create();
        $currency = factory(Currency::class)->states('GBP')->create();
        $timezone = factory(Timezone::class)->states('Europe/London')->create();
        $this->paymentGateway = factory(PaymentGateway::class)->states('Dummy')->create();

        // Setup base account information with correct country, currency and timezones
        $this->account = factory(Account::class)->create([
            'name'               => 'Local Integration Test Account',
            'timezone_id'        => $timezone->id, // London
            'currency_id'        => $currency->id, // Pound
            'country_id'         => $country->id, // UK
            'payment_gateway_id' => $this->paymentGateway->id, // Dummy
        ]);

        factory(AccountPaymentGateway::class)->create([
            'account_id'         => $this->account->id,
            'payment_gateway_id' => $this->paymentGateway->id,
        ]);

        $this->user = factory(User::class)->create([
            'account_id'    => $this->account->id,
            'email'         => 'local@test.com',
            'password'      => \Hash::make('pass'),
            'is_parent'     => true, // Top level user
            'is_registered' => true,
            'is_confirmed'  => true,
        ]);

        $this->organiserWithTax = factory(Organiser::class)->create([
            'account_id' => $this->account->id,
            'name'       => 'Test Organiser (With Tax)',
            'charge_tax' => true,
            'tax_name'   => 'VAT',
            'tax_value'  => 20.00,
        ]);

        $this->event = factory(Event::class)->create([
            'account_id'   => $this->account->id,
            'user_id'      => $this->user->id,
            'organiser_id' => $this->organiserWithTax->id,
            'title'        => 'Event without Fees',
            'currency_id'  => $currency->id, // Pound
            'is_live'      => true,
        ]);
        $this->eventWithPercentageFees = factory(Event::class)->create([
            'account_id'               => $this->account->id,
            'user_id'                  => $this->user->id,
            'organiser_id'             => $this->organiserWithTax->id,
            'title'                    => 'Event with Percentage Fees And Tax',
            'organiser_fee_fixed'      => 0.00,
            'organiser_fee_percentage' => 12.0,
            'currency_id'              => $currency->id, // Pound
            'is_live'                  => true,
        ]);

        $this->eventWithFixedFees = factory(Event::class)->create([
            'account_id'          => $this->account->id,
            'user_id'             => $this->user->id,
            'organiser_id'        => $this->organiserWithTax->id,
            'title'               => 'Event with Fixed Fees',
            'organiser_fee_fixed' => 3.50,
            'currency_id'         => $currency->id, // Pound
            'is_live'             => true,
        ]);
    }

    public function makeTicketOrder($count = 1, $price = 150.00, $hasPercentageFee = false, $hasFixedFee = false)
    {
        $salesVolume = (new Money($price))->multiply($count);

        // Every organization can have events with or without fees
        $eventId = $this->event->id;
        $organiserFees = new Money('0');
        if ($hasPercentageFee) {
            $eventId = $this->eventWithPercentageFees->id;
            $organiserFeePercentage = (new Money($this->eventWithPercentageFees->organiser_fee_percentage))->divide(100);
            $organiserFees = (new Money($price))->multiply($organiserFeePercentage);
        } else {
            if ($hasFixedFee) {
                $eventId = $this->eventWithFixedFees->id;
                $organiserFees = new Money($this->eventWithFixedFees->organiser_fee_fixed);
            }
        }

        $organiserFeesVolume = $organiserFees->multiply($count);

        // Work out the tax amount from the ticket prices and booking fees
        $organiserTaxRate = (new Money($this->organiserWithTax->tax_value))->divide(100);
        $subTotal = $salesVolume->add($organiserFeesVolume);
        $taxAmount = $subTotal->multiply($organiserTaxRate);

        $ticket = factory(Ticket::class)->create([
            'user_id'               => $this->user->id,
            'edited_by_user_id'     => $this->user->id,
            'account_id'            => $this->account->id,
            'order_id'              => null,
            'event_id'              => $eventId,
            'title'                 => 'Ticket',
            'price'                 => $price,
            'is_hidden'             => false,
            'quantity_sold'         => $count,
            'sales_volume'          => $salesVolume->toFloat(),
            'organiser_fees_volume' => $organiserFeesVolume->toFloat(),
        ]);

        $singleAttendeeOrder = factory(Order::class)->create([
            'account_id'            => $this->account->id,
            'payment_gateway_id'    => $this->paymentGateway->id,
            'order_status_id'       => OrderStatus::where('name', 'Completed')->first(), // Completed Order
            'discount'              => 0.00,
            'booking_fee'           => 0.00,
            'organiser_booking_fee' => $organiserFeesVolume->toFloat(),
            'amount'                => $salesVolume->toFloat(),
            'event_id'              => $eventId,
            'is_payment_received'   => true,
            'taxamt'                => $taxAmount,
        ]);

        $singleAttendeeOrder->tickets()->attach($ticket);

        factory(OrderItem::class)->create([
            'title'            => $ticket->title,
            'quantity'         => $count,
            'unit_price'       => $price,
            'unit_booking_fee' => $organiserFees->toFloat(),
            'order_id'         => $singleAttendeeOrder->id,
        ]);

        // Add the number of attendees based on the count
        $attendees = factory(Attendee::class, $count)->create([
            'order_id'   => $singleAttendeeOrder->id,
            'event_id'   => $eventId,
            'ticket_id'  => $ticket->id,
            'account_id' => $this->account->id,
        ]);

        factory(EventStats::class)->create([
            'date'                  => Carbon::now()->format('Y-m-d'),
            'views'                 => 0,
            'unique_views'          => 0,
            'tickets_sold'          => $count,
            'sales_volume'          => $salesVolume->toFloat(),
            'event_id'              => $eventId,
            'organiser_fees_volume' => $organiserFeesVolume->toFloat(),
        ]);

        return [$singleAttendeeOrder, $attendees];
    }

    public function getAccountUser()
    {
        return $this->user;
    }
}
