<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Description of Questions.
 *
 * @author Dave
 */
class Question extends MyBaseModel
{
    use SoftDeletes;

    /**
     * The events associated with the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany(\App\Models\Event::class);
    }

    /**
     * The type associated with the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function question_type()
    {
        return $this->belongsTo(\App\Models\QuestionType::class);
    }

    public function answers()
    {
        return $this->hasMany(\App\Models\QuestionAnswer::class);
    }

    /**
     * The options associated with the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function options()
    {
        return $this->hasMany(\App\Models\QuestionOption::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(\App\Models\Ticket::class);
    }

    /**
     * Scope a query to only include active questions.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsEnabled($query)
    {
        return $query->where('is_enabled', 1);
    }
}
