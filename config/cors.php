<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => explode(',', env('APP_CORS_PATHS', 'api/*,sanctum/csrf-cookie')),

    'allowed_methods' => explode(',', env('APP_CORS_ALLOWED_METHODS', '*')),

    'allowed_origins' => explode(',', env('APP_CORS_ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => explode(',', env('APP_CORS_ALLOWED_ORIGINS_PATTERNS', '')),

    'allowed_headers' => explode(',', env('APP_CORS_ALLOWED_HEADERS', '*')),

    'exposed_headers' => explode(',', env('APP_CORS_EXPOSED_HEADERS', '')),

    'max_age' => env('APP_CORS_MAX_AGE', 0),

    'supports_credentials' => env('APP_CORS_SUPPORTS_CREDENTIALS', false),

];
