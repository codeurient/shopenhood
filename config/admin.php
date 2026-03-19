<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Path
    |--------------------------------------------------------------------------
    | The URL prefix used to access the admin panel. Set this to a secret,
    | non-predictable string in your .env file and never commit the value.
    |
    | Example: ADMIN_PATH=manage-x7k2p9qr
    |
    | WARNING: Do not use "admin", "administrator", "backend", "cms", "panel",
    | or any other common/guessable string in production.
    |
    */
    'path' => env('ADMIN_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Admin Path Emergency Override
    |--------------------------------------------------------------------------
    | Setting ADMIN_PATH_OVERRIDE in .env bypasses the database-managed path
    | entirely. Use this if you are locked out and cannot access the database.
    | Remove it once you have recovered access.
    |
    */
    'path_override' => env('ADMIN_PATH_OVERRIDE'),

    /*
    |--------------------------------------------------------------------------
    | Admin IP Allowlist
    |--------------------------------------------------------------------------
    | Comma-separated list of IP addresses permitted to access the admin panel.
    | All other IPs will receive a 403 response before any auth check.
    | Leave empty (default) to allow all IPs and rely on authentication only.
    |
    | Example: ADMIN_IP_ALLOWLIST=203.0.113.10,198.51.100.5
    |
    */
    'ip_allowlist' => array_filter(
        array_map('trim', explode(',', env('ADMIN_IP_ALLOWLIST', '')))
    ),

    /*
    |--------------------------------------------------------------------------
    | Admin Login Rate Limiting
    |--------------------------------------------------------------------------
    | Maximum number of login attempts allowed per IP per minute before the
    | request is throttled with a 429 response.
    |
    */
    'login_rate_limit' => (int) env('ADMIN_LOGIN_RATE_LIMIT', 5),

];
