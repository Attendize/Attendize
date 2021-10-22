<?php

namespace App\Services;

use App\Models\Event;

class Order
{
    /**
     * @var float
     */
    private $orderTotal;

    /**
     * @var float
     */
    private $totalBookingFee;

    /**
     * @var Event
     */
    private $event;

    /**
     * @var float
     */
    public $orderTotalWithBookingFee;

    /**
     * @var float
     */
    public $taxAmount;

    /**
     * @var float
     */
    public $grandTotal;

    /**
     * Order constructor.
     * @param $orderTotal
     * @param $totalBookingFee
     * @param $event
     */
    public function __construct($orderTotal, $totalBookingFee, $event)
    {
        $this->orderTotal = $orderTotal;
        $this->totalBookingFee = $totalBookingFee;
        $this->event = $event;
    }

    /**
     * Calculates the final costs for an event and sets the various totals
     */
    public function calculateFinalCosts()
    {
        $this->orderTotalWithBookingFee = $this->orderTotal + $this->totalBookingFee;

        if ($this->event->organiser->charge_tax == 1) {
            $this->taxAmount = ($this->orderTotalWithBookingFee * $this->event->organiser->tax_value) / 100;
        } else {
            $this->taxAmount = 0;
        }

        $this->grandTotal = $this->orderTotalWithBookingFee + $this->taxAmount;
    }

    /**
     * @param bool $currencyFormatted
     * @return float|string
     */
    public function getOrderTotalWithBookingFee($currencyFormatted = false)
    {
        if ($currencyFormatted == false) {
            return number_format($this->orderTotalWithBookingFee, 2, '.', '');
        }

        return money($this->orderTotalWithBookingFee, $this->event->currency);
    }

    /**
     * @param bool $currencyFormatted
     * @return float|string
     */
    public function getTaxAmount($currencyFormatted = false)
    {
        if ($currencyFormatted == false) {
            return number_format($this->taxAmount, 2, '.', '');
        }

        return money($this->taxAmount, $this->event->currency);
    }

    /**
     * @param bool $currencyFormatted
     * @return float|string
     */
    public function getGrandTotal($currencyFormatted = false)
    {
        if ($currencyFormatted == false) {
            return number_format($this->grandTotal, 2, '.', '');
        }

        return money($this->grandTotal, $this->event->currency);
    }

    /**
     * @return string
     */
    public function getVatFormattedInBrackets()
    {
        return '(+'.$this->getTaxAmount(true).' '.$this->event->organiser->tax_name.')';
    }
}
