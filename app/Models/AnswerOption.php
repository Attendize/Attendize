<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerOption extends Model
{
    protected $fillable = [
        'question_answer_id',
        'question_option_id',
        'price',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function answer()
    {
        return $this->belongsTo(\App\Models\QuestionAnswer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function option()
    {
        return $this->belongsTo(\App\Models\QuestionOption::class);
    }
}
