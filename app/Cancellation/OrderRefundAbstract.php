<?php

namespace App\Cancellation;

use App\Models\Attendee;
use Log;
use Superbalist\Money\Money;

abstract class OrderRefundAbstract
{
    protected $order;
    protected $attendees;
    protected $currency;
    protected $organiserAmount;
    protected $refundedAmount;
    protected $maximumRefundableAmount;
    protected $organiserTaxRate;
    protected $refundAmount;
    protected $gateway;

    protected function setRefundAmounts()
    {
        $this->currency = $this->order->getEventCurrency();
        // Get the full order amount, tax and booking fees included
        $this->organiserAmount = new Money($this->order->organiser_amount, $this->currency);
        Log::debug(sprintf('Total Order Value: %s', $this->organiserAmount->display()));
        $this->refundedAmount = new Money($this->order->amount_refunded, $this->currency);
        Log::debug(sprintf('Already refunded amount: %s', $this->refundedAmount->display()));
        $this->maximumRefundableAmount = $this->organiserAmount->subtract($this->refundedAmount);
        Log::debug(sprintf('Maxmimum refundable amount: %s', $this->maximumRefundableAmount->display()));
        // We need the organiser tax value to calculate what the attendee would've paid
        $event = $this->order->event;
        $organiserTaxAmount = new Money($event->organiser->tax_value);
        $this->organiserTaxRate = $organiserTaxAmount->divide(100)->__toString();
        Log::debug(sprintf('Organiser Tax Rate: %s', $organiserTaxAmount->format().'%'));
        // Sets refund total based on attendees, their ticket prices and the organiser tax rate
        $this->setRefundTotal();
    }

    /**
     * Calculates the refund amount from the selected attendees from the ticket price perspective.
     *
     * It will add the tax value from the organiser if it's set and build the refund amount to equal
     * the amount of tickets purchased by the selected attendees. Ex:
     * Refunding 2 attendees @ 100EUR with 15% VAT = 230EUR
     */
    protected function setRefundTotal()
    {
        $organiserTaxRate = $this->organiserTaxRate;
        $currency = $this->currency;
        /**
         * Subtotal = (Ticket price + Organiser Fee)
         * Tax Amount = Subtotal * Tax rate
         * Refund Amount = Subtotal + Tax Amount
         */
        $this->refundAmount = new Money($this->attendees->map(function (Attendee $attendee) use (
            $organiserTaxRate,
            $currency
        ) {
            $ticketPrice = new Money($attendee->ticket->price, $currency);
            $organiserFee = new Money($attendee->event->getOrganiserFee($ticketPrice), $currency);
            $subTotal = $ticketPrice->add($organiserFee);
            Log::debug(sprintf('Ticket Price: %s', $ticketPrice->display()));
            Log::debug(sprintf('Ticket Organiser Fee: %s', $organiserFee->display()));
            Log::debug(sprintf('Ticket Tax: %s', $subTotal->multiply($organiserTaxRate)->display()));

            return $subTotal->add($subTotal->multiply($organiserTaxRate));
        })->reduce(function ($carry, $singleTicketWithTax) use ($currency) {
            $refundTotal = (new Money($carry, $currency));

            return $refundTotal->add($singleTicketWithTax)->format();
        }), $currency);
        Log::debug(sprintf('Requested Refund should include Tax: %s', $this->refundAmount->display()));
    }
}
