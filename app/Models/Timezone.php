<?php

namespace App\Models;

    /*
      Attendize.com   - Event Management & Ticketing
     */

/**
 * Description of Timezone.
 *
 * @author Dave
 */
class Timezone extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the model should use soft deletes.
     *
     * @var bool
     */
    protected $softDelete = false;
}
