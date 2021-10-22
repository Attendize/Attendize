<?php

namespace App\Cancellation;

use App\Models\Attendee;
use App\Models\EventStats;
use Log;
use Services\PaymentGateway\Factory;
use Superbalist\Money\Money;

class OrderRefund extends OrderRefundAbstract
{
    public function __construct($order, $attendees)
    {
        $this->order = $order;
        $this->attendees = $attendees;
        // We need to set the refund starting amounts first
        $this->setRefundAmounts();
        // Then we need to check for a valid refund state before we can continue
        $this->checkValidRefundState();
        $paymentGateway = $order->payment_gateway;
        $accountPaymentGateway = $order->account->getGateway($paymentGateway->id);
        $config = array_merge($accountPaymentGateway->config, [
            'testMode' => config('attendize.enable_test_payments'),
        ]);
        $this->gateway = (new Factory())->create($paymentGateway->name, $config);
    }

    public static function make($order, $attendees)
    {
        return new static($order, $attendees);
    }

    public function refund()
    {
        try {
            $response = $this->sendRefundRequest();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new OrderRefundException(trans('Controllers.refund_exception'));
        }
        if ($response['successful']) { // Successful is a Boolean
            // New refunded amount needs to be saved on the order
            $updatedRefundedAmount = $this->refundedAmount->add($this->refundAmount);
            // Update the amount refunded on the order
            $this->order->amount_refunded = $updatedRefundedAmount->toFloat();
            if ($this->organiserAmount->subtract($updatedRefundedAmount)->isZero()) {
                $this->order->is_refunded = true;
                // Order can't be both partially and fully refunded at the same time
                $this->order->is_partially_refunded = false;
                $this->order->order_status_id = config('attendize.order.refunded');
            } else {
                $this->order->is_partially_refunded = true;
                $this->order->order_status_id = config('attendize.order.partially_refunded');
            }
            // Persist the order refund updates
            $this->order->save();
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
        } else {
            throw new OrderRefundException($response['error_message']);
        }
    }

    /**
     * string
     */
    public function getRefundAmount()
    {
        return $this->refundAmount->format();
    }

    private function sendRefundRequest()
    {
        $response = $this->gateway->refundTransaction(
            $this->order,
            $this->refundAmount->toFloat(),
            floatval($this->order->booking_fee) > 0 ? true : false
        );
        Log::debug(strtoupper($this->order->payment_gateway->name), [
            'transactionReference' => $this->order->transaction_id,
            'amount' => $this->refundAmount->toFloat(),
            'refundApplicationFee' => floatval($this->order->booking_fee) > 0 ? true : false,
        ]);

        return $response;
    }

    private function checkValidRefundState()
    {
        $errorMessage = false;
        if (! $this->order->transaction_id) {
            $errorMessage = trans('Controllers.order_cant_be_refunded');
        }
        if ($this->order->is_refunded) {
            $errorMessage = trans('Controllers.order_already_refunded');
        } elseif ($this->maximumRefundableAmount->isZero()) {
            $errorMessage = trans('Controllers.nothing_to_refund');
        } elseif ($this->refundAmount->isGreaterThan($this->maximumRefundableAmount)) {
            // Error if the partial refund tries to refund more than allowed
            $errorMessage = trans('Controllers.maximum_refund_amount', [
                'money' => $this->maximumRefundableAmount->display(),
            ]);
        }
        if ($errorMessage) {
            throw new OrderRefundException($errorMessage);
        }
    }
}
