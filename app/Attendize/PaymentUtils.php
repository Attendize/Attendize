<?php

namespace App\Attendize;

/**
 * Class Payment
 *
 * Payment functions and utilities
 */
class PaymentUtils
{
    /**
     * The inverse of isFree function
     *
     * @param  int  $amount  Amount of money to check
     *
     * @return bool true if requires payment, false if not
     */
    public static function requiresPayment($amount)
    {
        return ! self::isFree($amount);
    }

    /**
     * Verify if a certain amount is free or not
     *
     * @param  int  $amount  Amount of money to check
     *
     * @return bool true if is free, false if not
     */
    public static function isFree($amount)
    {
        return ceil($amount) <= 0;
    }
}
