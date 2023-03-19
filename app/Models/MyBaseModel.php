<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
/*
 * Adapted from: https://github.com/hillelcoren/invoice-ninja/blob/master/app/models/EntityModel.php
 */

class MyBaseModel extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool $timestamps
     */
    public $timestamps = true;
    /**
     * Indicates whether the model uses soft deletes.
     *
     * @var bool $softDelete
     */
    protected $softDelete = true;
    /**
     * The validation rules of the model.
     *
     * @var array $rules
     */
    protected $rules = [];

    /**
     * The validation error messages of the model.
     *
     * @var array $messages
     */
    protected $messages = [];

    /**
     * The validation errors of model.
     *
     * @var  $errors
     */
    protected $errors;

    /**
     * Create a new model.
     *
     * @param  int|bool  $account_id
     * @param  int|bool  $user_id
     * @param  bool  $ignore_user_id
     *
     * @return \className
     */
    public static function createNew($account_id = false, $user_id = false, $ignore_user_id = false)
    {
        $className = static::class;
        $entity = new $className();

        if (Auth::check()) {
            if (!$ignore_user_id) {
                $entity->user_id = Auth::id();
            }

            $entity->account_id = Auth::user()->account_id;
        } elseif ($account_id || $user_id) {
            if ($user_id && !$ignore_user_id) {
                $entity->user_id = $user_id;
            }

            $entity->account_id = $account_id;
        } else {
            App::abort(500);
        }

        return $entity;
    }

    /**
     * Validate the model instance.
     *
     * @param $data
     *
     * @return bool
     */
    public function validate($data)
    {
        $rules = (method_exists($this, 'rules') ? $this->rules() : $this->rules);
        $v = Validator::make($data, $rules, $this->messages, $this->attributes);

        if ($v->fails()) {
            $this->errors = $v->messages();

            return false;
        }

        // validation pass
        return true;
    }

    /**
     * Gets the validation error messages.
     *
     * @param  bool  $returnArray
     *
     * @return mixed
     */
    public function errors($returnArray = true)
    {
        return $returnArray ? $this->errors->toArray() : $this->errors;
    }

    /**
     * Get a formatted date.
     *
     * @param        $field
     * @param  bool|null|string  $format
     *
     * @return bool|null|string
     */
    public function getFormattedDate($field, $format = false)
    {
        if (!$format) {
            $format = config('attendize.default_datetime_format');
        }

        return $this->$field === null ? null : $this->$field->format($format);
    }

    /**
     * Ensures each query looks for account_id
     *
     * @param $query
     * @param  bool  $accountId
     * @return mixed
     */
    public function scopeScope($query, $accountId = false)
    {

        /*
         * GOD MODE - DON'T UNCOMMENT!
         * returning $query before adding the account_id condition will let you
         * browse all events etc. in the system.
         * //return  $query;
         */

        if (!$accountId && Auth::check()) {
            $accountId = Auth::user()->account_id;
        }

        if ($accountId !== false) {
            $table = $this->getTable();

            $query->where(function ($query) use ($accountId, $table) {
                $query->whereRaw(\DB::raw('('.env('DB_TABLE_PREFIX', '').$table.'.account_id = '.$accountId.')'));
            });
        }

        return $query;
    }
}
