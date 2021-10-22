<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ApiBaseController extends Controller
{
    protected $account_id;

    public function __construct()
    {
        $this->account_id = Auth::guard('api')->check() ? Auth::guard('api')->user()->account_id : null;
    }
}
