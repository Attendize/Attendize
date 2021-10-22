<?php

namespace App\Models;

/*
  Attendize.com   - Event Management & Ticketing
 */

class Affiliate extends \Illuminate\Database\Eloquent\Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'visits',
        'tickets_sold',
        'event_id',
        'account_id',
        'sales_volume',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @return array $dates
     */
    public function getDates()
    {
        return ['created_at', 'updated_at'];
    }
}
