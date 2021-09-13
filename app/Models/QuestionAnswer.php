<?php

namespace App\Models;

class QuestionAnswer extends MyBaseModel
{

    protected $fillable = [
        'question_id',
        'event_id',
        'attendee_id',
        'account_id',
        'answer_text',
        'questionable_id',
        'questionable_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function event()
    {
        return $this->belongsToMany(\App\Models\Event::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(\App\Models\Question::class)->withTrashed();
    }

    /**
     * @return float
     */
    public function getPriceAttribute()
    {
        $price = 0;
        foreach ($this->answeredOptions as $option) {
            $price += $option->price;
        }
        return $price;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answeredOptions()
    {
        return $this->hasMany(\App\Models\AnswerOption::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attendee()
    {
        return $this->belongsTo(\App\Models\Attendee::class);
    }

}
