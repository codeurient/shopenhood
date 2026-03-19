<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\ListingCardStatsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ListingCardStatsService::class);
    }

    public function boot(): void
    {
        $this->loadAdminPath();
    }

    /**
     * Resolve the admin panel path from (in priority order):
     *   1. ADMIN_PATH_OVERRIDE env var  — emergency hard-override, bypasses DB
     *   2. Encrypted value in settings table, cached in app cache
     *   3. ADMIN_PATH env var           — falls through from config/admin.php default
     *
     * This runs before RouteServiceProvider::boot() so routes pick up the value.
     */
    private function loadAdminPath(): void
    {
        // 1 — hard override (emergency recovery)
        if ($override = config('admin.path_override')) {
            Config::set('admin.path', $override);

            return;
        }

        // 2 — DB-managed path (cached)
        try {
            $path = Cache::remember('admin.access_path', now()->addDay(), function () {
                $encrypted = Setting::getValue('admin.access_path', null);

                if (! $encrypted) {
                    return null;
                }

                return decrypt($encrypted);
            });

            if ($path) {
                Config::set('admin.path', $path);
            }
        } catch (\Throwable) {
            // DB not available, decryption failure, etc. — silently fall through to env
        }
    }
}
