<?php

namespace App\Cancellation;

use App\Models\Attendee;
use App\Models\Order;
use Superbalist\Money\Money;

class OrderCancellation
{
    /** @var Order */
    private $order;
    /** @var array */
    private $attendees;
    /** @var OrderRefund */
    private $orderRefund;

    /**
     * OrderCancellation constructor.
     *
     * @param Order $order
     * @param $attendees
     */
    public function __construct(Order $order, $attendees)
    {
        $this->order = $order;
        $this->attendees = $attendees;
    }

    /**
     * Create a new instance to be used statically
     *
     * @param Order $order
     * @param $attendees
     * @return OrderCancellation
     */
    public static function make(Order $order, $attendees): self
    {
        return new static($order, $attendees);
    }

    /**
     * Cancels an order
     *
     * @throws OrderRefundException
     */
    public function cancel(): void
    {
        $orderAwaitingPayment = false;
        if ($this->order->order_status_id == config('attendize.order.awaiting_payment')) {
            $orderAwaitingPayment = true;
            $orderCancel = OrderCancel::make($this->order, $this->attendees);
            $orderCancel->cancel();
        }
        // If order can do a refund then refund first
        if ($this->order->canRefund() && ! $orderAwaitingPayment) {
            $orderRefund = OrderRefund::make($this->order, $this->attendees);
            $orderRefund->refund();
            $this->orderRefund = $orderRefund;
        }
        // TODO if no refunds can be done, mark the order as cancelled to indicate attendees are cancelled
        // Cancel the attendees
        $this->attendees->map(static function (Attendee $attendee) {
            $attendee->is_cancelled = true;
            $attendee->save();
        });
    }

    /**
     * Returns the return amount
     *
     * @return Money
     */
    public function getRefundAmount()
    {
        if ($this->orderRefund === null) {
            return new Money('0');
        }

        return $this->orderRefund->getRefundAmount();
    }
}
