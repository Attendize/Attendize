<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Str;
use Image;

class Organiser extends MyBaseModel implements AuthenticatableContract
{
    use Authenticatable;
    /**
     * The validation rules for the model.
     *
     * @var array $rules
     */
    protected $rules = [
        'name'           => ['required'],
        'email'          => ['required', 'email'],
        'organiser_logo' => ['nullable', 'mimes:jpeg,jpg,png', 'max:10000'],
    ];

    protected $extra_rules = [
        'tax_name'        => ['nullable', 'max:15'],
        'tax_value'       => ['nullable', 'numeric'],
        'tax_id'          => ['nullable', 'max:100'],
    ];

    /**
     * The validation rules for the model.
     *
     * @var array $attributes
     */
    protected $attributes = [
        'tax_name'        => 'Tax Name',
        'tax_value'       => 'Tax Rate',
        'tax_id'          => 'Tax ID',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['events'];

    /**
     * The validation error messages for the model.
     *
     * @var array $messages
     */
    protected $messages = [
        'name.required'        => 'You must at least give a name for the event organiser.',
        'organiser_logo.max'   => 'Please upload an image smaller than 10Mb',
        'organiser_logo.size'  => 'Please upload an image smaller than 10Mb',
        'organiser_logo.mimes' => 'Please select a valid image type (jpeg, jpg, png)',
    ];

    /**
     * The account associated with the organiser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * The events associated with the organizer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(\App\Models\Event::class);
    }

    /**
     * The users associated with the organizer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    /**
     * The attendees associated with the organizer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function attendees()
    {
        return $this->hasManyThrough(\App\Models\Attendee::class, \App\Models\Event::class);
    }

    /**
     * Get the orders related to an organiser
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function orders()
    {
        return $this->hasManyThrough(\App\Models\Order::class, \App\Models\Event::class);
    }

    /**
     * Get the full logo path of the organizer.
     *
     * @return mixed|string
     */
    public function getFullLogoPathAttribute()
    {
        if ($this->logo_path && (file_exists(public_path($this->logo_path)) || file_exists(config('attendize.cdn_url_user_assets') . '/' . $this->logo_path))) {
            return config('attendize.cdn_url_user_assets') . '/' . $this->logo_path;
        }

        return config('attendize.fallback_organiser_logo_url');
    }

    /**
     * Get the url of the organizer.
     *
     * @return string
     */
    public function getOrganiserUrlAttribute()
    {
        return route('showOrganiserHome', [
            'organiser_id'   => $this->id,
            'organiser_slug' => Str::slug($this->oraganiser_name),
        ]);
    }

    /**
     * Get the sales volume of the organizer.
     *
     * @return mixed|number
     */
    public function getOrganiserSalesVolumeAttribute()
    {
        return $this->events->sum('sales_volume');
    }

    public function getTicketsSold()
    {
        return $this->attendees()->where('is_cancelled', false)->count();
    }

    /**
     * TODO:implement DailyStats method
     */
    public function getDailyStats()
    {
    }


    /**
     * Set a new Logo for the Organiser
     *
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function setLogo(UploadedFile $file)
    {
        $filename = Str::slug($this->name).'-logo-'.$this->id.'.'.strtolower($file->getClientOriginalExtension());

        // Image Directory
        $imageDirectory = public_path() . '/' . config('attendize.organiser_images_path');

        // Paths
        $relativePath = config('attendize.organiser_images_path').'/'.$filename;
        $absolutePath = public_path($relativePath);

        $file->move($imageDirectory, $filename);

        $img = Image::make($absolutePath);

        $img->resize(250, 250, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $img->save($absolutePath);

        if (file_exists($absolutePath)) {
            $this->logo_path = $relativePath;
        }
    }

    /**
     * Adds extra validator rules to the organiser object depending on whether tax is required or not
     */
    public function addExtraValidationRules() {
        $this->rules = array_merge($this->rules, $this->extra_rules);
    }
}

