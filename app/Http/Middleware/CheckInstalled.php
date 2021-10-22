<?php

namespace App\Http\Middleware;

use App\Attendize\Utils;
use App\Models\Account;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         * Check if the 'installed' file has been created
         */
        if (! Utils::isAttendize() && ! Utils::installed()) {
            return Redirect::to('install');
        }

        /*
         * Redirect user to signup page if there are no accounts
         */
        if (Account::count() === 0 && ! $request->is('signup*')) {
            return redirect()->to('signup');
        }

        return $next($request);
    }
}
