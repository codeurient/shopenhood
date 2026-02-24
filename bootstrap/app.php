<?php

/*
|--------------------------------------------------------------------------
| Force .env Values Over Any Pre-existing Server Environment
|--------------------------------------------------------------------------
|
| Laravel's default createImmutable() loader will NOT override environment
| variables that the web server process already has set (e.g. stale Forge
| credentials inherited by an Apache worker). By running createMutable()
| first we guarantee that the values in .env always win, exactly as they
| do when running Artisan from the command line.
|
*/

// Apache worker processes may inherit stale env vars (e.g. DB_USERNAME=forge from
// a previous Forge install). createImmutable() won't override those, so we use
// createMutable() here — but ONLY for web requests. CLI runs (artisan, tests) have
// a clean process environment and must not have their env vars overwritten, as
// PHPUnit sets APP_ENV=testing before the application boots.
if (PHP_SAPI !== 'cli' && file_exists(dirname(__DIR__).'/.env')) {
    \Dotenv\Dotenv::createMutable(dirname(__DIR__))->safeLoad();
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
