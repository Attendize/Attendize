<?php

declare(strict_types=1);

if (!function_exists('public_route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  array|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function public_route($name, $parameters = [], $absolute = true)
    {
        $urlGenerator = app('url');

        if (!$absolute) {
            return $urlGenerator->route($name, $parameters, $absolute);
        }

        $appUrl = env('APP_URL', $urlGenerator->getRequest()->root());
        $publicUrl = env('PUBLIC_URL', '');

        if (isset($publicUrl) && $publicUrl !== '') {
            // Temporary replace default app domain with public domain
            $route = Route::getRoutes()->getByName($name)->domain($publicUrl);
            // Generate public url string
            $result = $urlGenerator->toRoute($route, $parameters, true);
            // Set domain to default value
            $route = Route::getRoutes()->getByName($name)->domain($appUrl);
        }

        return $result;
    }
}
