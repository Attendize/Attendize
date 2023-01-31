<?php

declare(strict_types=1);

if (!function_exists('app_url')) {
    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    function app_url($path, $extra = [], $secure = null)
    {
        $urlGenerator = app('url');
        $appUrl = env('APP_URL', '');

        if (isset($appUrl) && $appUrl !== '') {
            $parsedPath = parse_url($path);
            $parsedAppUrl = parse_url($appUrl);

            if ($parsedAppUrl) {
                $parsedPath['host'] = $parsedAppUrl['host'];
                $parsedPath['port'] = isset($parsedAppUrl['port']) ? $parsedAppUrl['port'] : null;
            } else {
                $parsedPath['host'] = $appUrl;
                $parsedPath['port'] = null;
            }

            // Temporary replace default app domain with public domain
            $urlGenerator->forceRootUrl($parsedPath['host']);

            $path = unparse_url($parsedPath);
        }

        // Generate public url string
        $result = $urlGenerator->to($path, $extra, $secure);

        if (isset($appUrl) && $appUrl !== '') {
            // Reset domain to default value
            $urlGenerator->forceRootUrl('');
        }

        return $result;
    }
}
