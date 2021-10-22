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

trait OrganisationWithoutTax
{
    private $account;
    private $paymentGateway;
    private $user;
    private $event;
    private $eventWithPercentageFees;
    private $eventWithFixedFees;

    public function setupOrganisationWithoutTax()
    {
        $orderStatuses = collect([
            ['id' => config('attendize.order.complete'), 'name' => 'Completed'],
            ['id' => config('attendize.order.refunded'), 'name' => 'Refunded'],
            ['id' => config('attendize.order.partially_refunded'), 'name' => 'Partially Refunded'],
            ['id' => config('attendize.order.cancelled'), 'name' => 'Cancelled'],
        ]);
        $orderStatuses->map(function ($orderStatus) {
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
            'name' => 'Local Integration Test Account',
            'timezone_id' => $timezone->id, // London
            'currency_id' => $currency->id, // Pound
            'country_id' => $country->id, // UK
            'payment_gateway_id' => $this->paymentGateway->id, // Dummy
        ]);

        factory(AccountPaymentGateway::class)->create([
            'account_id' => $this->account->id,
            'payment_gateway_id' => $this->paymentGateway->id,
        ]);

        $this->user = factory(User::class)->create([
            'account_id' => $this->account->id,
            'email' => 'local@test.com',
            'password' => \Hash::make('pass'),
            'is_parent' => true, // Top level user
            'is_registered' => true,
            'is_confirmed' => true,
        ]);

        $organiserNoTax = factory(Organiser::class)->create([
            'account_id' => $this->account->id,
            'name' => 'Test Organiser (No Tax)',
            'charge_tax' => false,
            'tax_name' => '',
            'tax_value' => 0.00,
        ]);

        $this->event = factory(Event::class)->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'organiser_id' => $organiserNoTax->id,
            'title' => 'Event without Fees',
            'currency_id' => $currency->id, // Pound
            'is_live' => true,
        ]);

        $this->eventWithPercentageFees = factory(Event::class)->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'organiser_id' => $organiserNoTax->id,
            'title' => 'Event with Percentage Fees',
            'organiser_fee_percentage' => 5.0,
            'currency_id' => $currency->id, // Pound
            'is_live' => true,
        ]);

        $this->eventWithFixedFees = factory(Event::class)->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'organiser_id' => $organiserNoTax->id,
            'title' => 'Event with Fixed Fees',
            'organiser_fee_fixed' => 2.50,
            'currency_id' => $currency->id, // Pound
            'is_live' => true,
        ]);
    }

    public function makeTicketOrder($count = 1, $price = 100.00, $hasPercentageFee = false, $hasFixedFee = false)
    {
        $salesVolume = (new Money($price))->multiply($count)->toFloat();
        // Every organisation can have events with or without fees
        $eventId = $this->event->id;
        $organiserFees = new Money('0');
        if ($hasPercentageFee) {
            $eventId = $this->eventWithPercentageFees->id;
            $organiserFeePercentage = (new Money($this->eventWithPercentageFees->organiser_fee_percentage))->divide(100);
            $organiserFees = (new Money($price))->multiply($organiserFeePercentage);
        } elseif ($hasFixedFee) {
            $eventId = $this->eventWithFixedFees->id;
            $organiserFees = new Money($this->eventWithFixedFees->organiser_fee_fixed);
        }

        $ticket = factory(Ticket::class)->create([
            'user_id' => $this->user->id,
            'edited_by_user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'order_id' => null,
            'event_id' => $eventId,
            'title' => 'Ticket',
            'price' => $price,
            'is_hidden' => false,
            'quantity_sold' => $count,
            'sales_volume' => $salesVolume,
            'organiser_fees_volume' => $organiserFees->multiply($count)->toFloat(),
        ]);

        $singleAttendeeOrder = factory(Order::class)->create([
            'account_id' => $this->account->id,
            'payment_gateway_id' => $this->paymentGateway->id,
            'order_status_id' => OrderStatus::where('name', 'Completed')->first(), // Completed Order
            'discount' => 0.00,
            'booking_fee' => 0.00,
            'organiser_booking_fee' => $organiserFees->multiply($count)->toFloat(),
            'amount' => $salesVolume,
            'event_id' => $eventId,
            'is_payment_received' => true,
        ]);

        $singleAttendeeOrder->tickets()->attach($ticket);

        factory(OrderItem::class)->create([
            'title' => $ticket->title,
            'quantity' => $count,
            'unit_price' => $price,
            'unit_booking_fee' => $organiserFees->toFloat(),
            'order_id' => $singleAttendeeOrder->id,
        ]);

        // Add the number of attendees based on the count
        $attendees = factory(Attendee::class, $count)->create([
            'order_id' => $singleAttendeeOrder->id,
            'event_id' => $eventId,
            'ticket_id' => $ticket->id,
            'account_id' => $this->account->id,
        ]);

        factory(EventStats::class)->create([
            'date' => Carbon::now()->format('Y-m-d'),
            'views' => 0,
            'unique_views' => 0,
            'tickets_sold' => $count,
            'sales_volume' => $salesVolume,
            'event_id' => $eventId,
            'organiser_fees_volume' => $organiserFees->multiply($count)->toFloat(),
        ]);

        return [$singleAttendeeOrder, $attendees];
    }

    public function getAccountUser()
    {
        return $this->user;
    }
}
