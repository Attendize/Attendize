<?php

namespace Tests\Features;

use App\Models\Attendee;
use Tests\Concerns\OrganisationWithTax;
use Tests\TestCase;

class OrganisationWithTaxTest extends TestCase
{
    use OrganisationWithTax;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\FirstRunMiddleware::class,
        ]);
        $this->setupOrganisationWithTax();
    }

    /**
     * @test
     */
    public function cancels_and_refunds_order_with_single_ticket_and_tax()
    {
        // Setup single attendee order
        [$order, $attendees] = $this->makeTicketOrder(1, 150.00);
        $attendeeIds = $attendees->pluck('id')->toArray();
        $response = $this->actingAs($this->getAccountUser())
            ->post("event/order/$order->id/cancel", [
                'attendees' => [$attendeeIds[0]],
            ]);

        // Check refund call works
        $response->assertStatus(200);
        // Assert database is correct after refund and cancel
        $this->assertDatabaseHasMany([
            'event_stats' => [
                'tickets_sold'          => 0,
                'sales_volume'          => 0.00,
                'organiser_fees_volume' => 0.00,
            ],
            'tickets'     => [
                'sales_volume'          => 0.00,
                'organiser_fees_volume' => 0.00,
                'quantity_sold'         => 0,
            ],
            'orders'      => [
                'organiser_booking_fee' => 0.00,
                'amount'                => 150.00,
                'amount_refunded'       => 180.00,
                'taxamt'                => 30.00,
                'is_refunded'           => true,
            ],
            'attendees'   => [
                'is_refunded'  => true,
                'is_cancelled' => true,
            ],
        ]);
    }

    /**
     * @test
     */
    public function cancels_and_refunds_order_with_multiple_tickets_and_tax()
    {
        // Setup multiple attendee order but refund only 3 out of 5
        [$order, $attendees] = $this->makeTicketOrder(5, 150.00);
        $attendeeIds = $attendees->pluck('id')->toArray();
        $response = $this->actingAs($this->getAccountUser())
            ->post("event/order/$order->id/cancel", [
                'attendees' => [
                    $attendeeIds[0],
                    $attendeeIds[1],
                    $attendeeIds[2],
                ],
            ]);

        // Check refund call works
        $response->assertStatus(200);
        // Assert database is correct after refund and cancel
        $this->assertDatabaseHasMany([
            'event_stats' => [
                'tickets_sold'          => 2,
                'sales_volume'          => 300.00,
                'organiser_fees_volume' => 0.00,
            ],
            'tickets'     => [
                'sales_volume'          => 300.00,
                'organiser_fees_volume' => 0.00,
                'quantity_sold'         => 2,
            ],
            'orders'      => [
                'organiser_booking_fee' => 0.00,
                'amount'                => 750.00,
                'amount_refunded'       => 540.00,
                'taxamt'                => 150.00,
                'is_partially_refunded' => true,
            ],
        ]);

        // Check that the the attendees are marked as refunded/cancelled
        $this->assertTrue(Attendee::find($attendeeIds[0])->is_refunded);
        $this->assertTrue(Attendee::find($attendeeIds[0])->is_cancelled);
        // Last attendee in order will not be refunded and cancelled
        $this->assertFalse(Attendee::find($attendeeIds[4])->is_refunded);
        $this->assertFalse(Attendee::find($attendeeIds[4])->is_cancelled);
    }

    /**
     * @test
     */
    public function cancels_and_refunds_order_with_single_ticket_with_tax_and_percentage_booking_fees()
    {
        // Setup single attendee order with % fees
        [$order, $attendees] = $this->makeTicketOrder(1, 150.00, true);
        $attendeeIds = $attendees->pluck('id')->toArray();
        $response = $this->actingAs($this->getAccountUser())
            ->post("event/order/$order->id/cancel", [
                'attendees' => [$attendeeIds[0]],
            ]);

        // Check refund call works
        $response->assertStatus(200);
        // Assert database is correct after refund and cancel
        $this->assertDatabaseHasMany([
            'event_stats' => [
                'tickets_sold'          => 0,
                'sales_volume'          => 0.00,
                'organiser_fees_volume' => 0.00,
            ],
            'tickets'     => [
                'sales_volume'          => 0.00,
                'organiser_fees_volume' => 0.00,
                'quantity_sold'         => 0,
            ],
            'orders'      => [
                'organiser_booking_fee' => 18.00, // 12% fee
                'amount'                => 150.00,
                'amount_refunded'       => 201.60,
                'taxamt'                => 33.6, // 20% VAT
                'is_refunded'           => true,
            ],
            'attendees'   => [
                'is_refunded'  => true,
                'is_cancelled' => true,
            ],
        ]);
    }

    /**
     * @test
     */
    public function cancels_and_refunds_order_with_multiple_tickets_with_tax_and_percentage_booking_fees()
    {
        // Setup single attendee order with % fees
        [$order, $attendees] = $this->makeTicketOrder(5, 120.00, true);
        $attendeeIds = $attendees->pluck('id')->toArray();
        $response = $this->actingAs($this->getAccountUser())
            ->post("event/order/$order->id/cancel", [
                'attendees' => [
                    $attendeeIds[0],
                    $attendeeIds[1],
                    $attendeeIds[2],
                ],
            ]);

        // Check refund call works
        $response->assertStatus(200);
        // Assert database is correct after refund and cancel

        $eventStats = \App\Models\EventStats::first()
            ->only('tickets_sold', 'sales_volume', 'organiser_fees_volume');
        $this->assertEquals([
            'tickets_sold'          => 2,
            'sales_volume'          => 240,
            'organiser_fees_volume' => 28.8, // 12% Fees
        ], $eventStats);

        $tickets = \App\Models\Ticket::first()
            ->only('sales_volume', 'organiser_fees_volume', 'quantity_sold');
        $this->assertEquals([
            'quantity_sold'         => 2,
            'sales_volume'          => 240,
            'organiser_fees_volume' => 28.8, // 12% Fees
        ], $tickets);

        $order = \App\Models\Order::first()
            ->only('organiser_booking_fee', 'amount', 'amount_refunded', 'taxamt', 'is_partially_refunded');
        $this->assertEquals([
            'organiser_booking_fee' => 72.00, // 12% Fees
            'amount'                => 600.00,
            'amount_refunded'       => 483.84,
            'taxamt'                => 134.40, // 20% VAT
            'is_partially_refunded' => true,
        ], $order);

        // Check that the the attendees are marked as refunded/cancelled
        $this->assertTrue(Attendee::find($attendeeIds[0])->is_refunded);
        $this->assertTrue(Attendee::find($attendeeIds[0])->is_cancelled);
        // Last attendee in order will not be refunded and cancelled
        $this->assertFalse(Attendee::find($attendeeIds[4])->is_refunded);
        $this->assertFalse(Attendee::find($attendeeIds[4])->is_cancelled);
    }

    /**
     * @test
     */
    public function cancels_and_refunds_order_with_single_ticket_with_tax_and_fixed_booking_fees()
    {
        // Setup single attendee order with % fees
        [$order, $attendees] = $this->makeTicketOrder(1, 50.00, false, true);
        $attendeeIds = $attendees->pluck('id')->toArray();
        $response = $this->actingAs($this->getAccountUser())
            ->post("event/order/$order->id/cancel", [
                'attendees' => [$attendeeIds[0]],
            ]);

        // Check refund call works
        $response->assertStatus(200);
        // Assert database is correct after refund and cancel
        $this->assertDatabaseHasMany([
            'event_stats' => [
                'tickets_sold'          => 0,
                'sales_volume'          => 0.00,
                'organiser_fees_volume' => 0.00,
            ],
            'tickets'     => [
                'sales_volume'          => 0.00,
                'organiser_fees_volume' => 0.00,
                'quantity_sold'         => 0,
            ],
            'orders'      => [
                'organiser_booking_fee' => 3.50, // Fixed fee
                'amount'                => 50.00,
                'amount_refunded'       => 64.20,
                'taxamt'                => 10.70, // 20% VAT
                'is_refunded'           => true,
            ],
            'attendees'   => [
                'is_refunded'  => true,
                'is_cancelled' => true,
            ],
        ]);
    }

    /**
     * @test
     */
    public function cancels_and_refunds_order_with_multiple_tickets_with_tax_and_fixed_booking_fees()
    {
        // Setup single attendee order with % fees
        [$order, $attendees] = $this->makeTicketOrder(5, 240.00, false, true);
        $attendeeIds = $attendees->pluck('id')->toArray();
        $response = $this->actingAs($this->getAccountUser())
            ->post("event/order/$order->id/cancel", [
                'attendees' => [
                    $attendeeIds[0],
                    $attendeeIds[1],
                ],
            ]);

        // Check refund call works
        $response->assertStatus(200);
        // Assert database is correct after refund and cancel
        $this->assertDatabaseHasMany([
            'event_stats' => [
                'tickets_sold'          => 3,
                'sales_volume'          => 720.00,
                'organiser_fees_volume' => 10.50, // Fixed fee 3.50
            ],
            'tickets'     => [
                'sales_volume'          => 720.00,
                'organiser_fees_volume' => 10.50,
                'quantity_sold'         => 3,
            ],
            'orders'      => [
                'organiser_booking_fee' => 17.50, // Fixed fee 3.50
                'amount'                => 1200.00,
                'amount_refunded'       => 584.40,
                'taxamt'                => 243.50, // 20% VAT
                'is_partially_refunded' => true,
            ],
        ]);

        // Check that the the attendees are marked as refunded/cancelled
        $this->assertTrue(Attendee::find($attendeeIds[0])->is_refunded);
        $this->assertTrue(Attendee::find($attendeeIds[0])->is_cancelled);
        $this->assertFalse(Attendee::find($attendeeIds[2])->is_refunded);
        $this->assertFalse(Attendee::find($attendeeIds[2])->is_cancelled);
        // Last attendee in order will not be refunded and cancelled
        $this->assertFalse(Attendee::find($attendeeIds[4])->is_refunded);
        $this->assertFalse(Attendee::find($attendeeIds[4])->is_cancelled);
    }
}
