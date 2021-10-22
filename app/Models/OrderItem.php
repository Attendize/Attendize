<?php

namespace App\Models;

    /*
      Attendize.com   - Event Management & Ticketing
     */

/**
 * Description of OrderItems.
 *
 * @author Dave
 */
class OrderItem extends MyBaseModel
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'quantity',
        'order_id',
        'unit_price',
    ];
}
