<?php

namespace App\Cancellation;

use App\Models\Attendee;
use App\Models\EventStats;
use Log;
use Services\PaymentGateway\Factory;
use Superbalist\Money\Money;

class OrderCancel extends OrderRefundAbstract
{
    public function __construct($order, $attendees)
    {
        $this->order = $order;
        $this->attendees = $attendees;
        // We need to set the refund starting amounts first
        $this->setRefundAmounts();
    }

    public static function make($order, $attendees)
    {
        return new static($order, $attendees);
    }

    public function cancel()
    {
        // New refunded amount needs to be saved on the order
        $updatedRefundedAmount = $this->refundedAmount->add($this->refundAmount);
        // Update the amount refunded on the order
        $this->order->amount_refunded = $updatedRefundedAmount->toFloat();
        if ($this->organiserAmount->subtract($updatedRefundedAmount)->isZero()) {
            $this->order->is_cancelled = true;
            // Order can't be both partially and fully refunded at the same time
            $this->order->is_partially_refunded = false;
            $this->order->order_status_id = config('attendize.order.cancelled');
        }
        $this->order->save();
        // Persist the order refund updates
        // With the refunds done, we can mark the attendees as cancelled and refunded as well
        $currency = $this->currency;
        $this->attendees->map(function (Attendee $attendee) use ($currency) {
            $ticketPrice = new Money($attendee->ticket->price, $currency);
            $attendee->ticket->decrement('quantity_sold', 1);
            $attendee->ticket->decrement('sales_volume', $ticketPrice->toFloat());
            $organiserFee = $attendee->event->getOrganiserFee($ticketPrice);
            $attendee->ticket->decrement('organiser_fees_volume', $organiserFee->toFloat());
            $attendee->is_refunded = true;
            $attendee->save();
            /** @var EventStats $eventStats */
            $eventStats = EventStats::where('event_id', $attendee->event_id)
                ->where('date', $attendee->created_at->format('Y-m-d'))
                ->first();
            if ($eventStats) {
                $eventStats->decrement('tickets_sold', 1);
                $eventStats->decrement('sales_volume', $ticketPrice->toFloat());
                $eventStats->decrement('organiser_fees_volume', $organiserFee->toFloat());
            }
        });
    }
}
