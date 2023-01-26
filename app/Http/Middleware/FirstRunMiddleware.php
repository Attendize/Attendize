<?php

namespace App\Http\Middleware;

use App\Models\Organiser;
use Closure;
use Auth;

class FirstRunMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         * Super Admin users should see organiser views.
         *
         * If there are no organisers then redirect the user to create one
         * else - if there's only one organiser bring the user straight there.
         */
        if ($this->accountHasNoOrganisers($request)) {
            return redirect(route('showCreateOrganiser', ['first_run' => 'yup']));
        } elseif ($this->accountHasOneOrganiser($request)) {
            return redirect(route('showOrganiserDashboard', ['organiser_id' => Organiser::scope()->first()->id]));
        }

        return $next($request);
    }

    private function accountHasNoOrganisers($request): bool
    {
        return (Organiser::scope()->count() === 0
            && !($request->route()->getName() === 'showCreateOrganiser')
            && !($request->route()->getName() === 'postCreateOrganiser')
        );
    }

    private function accountHasOneOrganiser($request): bool {
        return (Organiser::scope()->count() === 1
            && ($request->route()->getName() === 'showSelectOrganiser')
        );
    }
}
