<?php

namespace App\Http\Middleware;

use App\Models\Organiser;
use Auth;
use Closure;

class CanManageOrganisers
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
        /** @var $user App\Models\User */
        $user = Auth::user();

        // Normal users should not be able to see organiser views
        if (!$user->can('manage organisers')) {
            if ($this->accountHasNoOrganisers($request)) {
                abort(403, 'No organisers can be found. Please ask your system administrator to finish setup.');
            }

            // Normal users should not be able to switch between organisers
            return redirect(route('index'));
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
};
