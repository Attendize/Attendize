<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

class UserApiController extends ApiBaseController
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request)
    {
        return $request->user();
    }

}
