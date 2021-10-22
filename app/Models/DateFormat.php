<?php

namespace App\Models;

    /*
      Attendize.com   - Event Management & Ticketing
     */

/**
 * Description of DateFormat.
 *
 * @author Dave
 */
class DateFormat extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Indicates whether the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates whether the model should use soft deletes.
     *
     * @var bool
     */
    protected $softDelete = false;
}
