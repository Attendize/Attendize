<?php

namespace Tests\Features;

use App\Models\Attendee;
use Tests\Concerns\OrganisationWithoutTax;
use Tests\TestCase;

class OrganisationWithoutTaxTest extends TestCase
{
    use OrganisationWithoutTax;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\FirstRunMiddleware::class,
        ]);
        $this->setupOrganisationWithoutTax();
    }

    /**
     * @test
     */
    public function cancels_and_refunds_order_with_single_ticket()
    {
        // Setup single attendee order
        [$order, $attendees] = $this->makeTicketOrder(1, 100.00);
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
                'amount'                => 100.00,
                'amount_refunded'       => 100.00,
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
    public function cancels_and_refunds_order_with_multiple_tickets()
    {
        // Setup multiple attendee order but refund only 3 out of 5
        [$order, $attendees] = $this->makeTicketOrder(5, 100.00);
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
                'sales_volume'          => 200.00,
                'organiser_fees_volume' => 0.00,
            ],
            'tickets'     => [
                'sales_volume'          => 200.00,
                'organiser_fees_volume' => 0.00,
                'quantity_sold'         => 2,
            ],
            'orders'      => [
                'organiser_booking_fee' => 0.00,
                'amount'                => 500.00,
                'amount_refunded'       => 300.00,
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
    public function cancels_and_refunds_order_with_single_ticket_with_percentage_booking_fees()
    {
        // Setup single attendee order with % fees
        [$order, $attendees] = $this->makeTicketOrder(1, 100.00, true);
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
                'organiser_booking_fee' => 5.00,
                'amount'                => 100.00,
                'amount_refunded'       => 105.00,
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
    public function cancels_and_refunds_order_with_multiple_tickets_with_percentage_booking_fees()
    {
        // Setup single attendee order with % fees
        [$order, $attendees] = $this->makeTicketOrder(5, 100.00, true);
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
                'sales_volume'          => 200.00,
                'organiser_fees_volume' => 10.00,
            ],
            'tickets'     => [
                'sales_volume'          => 200.00,
                'organiser_fees_volume' => 10.00,
                'quantity_sold'         => 2,
            ],
            'orders'      => [
                'organiser_booking_fee' => 25.00,
                'amount'                => 500.00,
                'amount_refunded'       => 315.00,
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
    public function cancels_and_refunds_order_with_single_ticket_with_fixed_booking_fees()
    {
        // Setup single attendee order with % fees
        [$order, $attendees] = $this->makeTicketOrder(1, 100.00, false, true);
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
                'organiser_booking_fee' => 2.50,
                'amount'                => 100.00,
                'amount_refunded'       => 102.50,
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
    public function cancels_and_refunds_order_with_multiple_tickets_with_fixed_booking_fees()
    {
        // Setup single attendee order with % fees
        [$order, $attendees] = $this->makeTicketOrder(5, 100.00, false, true);
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
                'sales_volume'          => 200.00,
                'organiser_fees_volume' => 5.00,
            ],
            'tickets'     => [
                'sales_volume'          => 200.00,
                'organiser_fees_volume' => 5.00,
                'quantity_sold'         => 2,
            ],
            'orders'      => [
                'organiser_booking_fee' => 12.50,
                'amount'                => 500.00,
                'amount_refunded'       => 307.50,
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
}
