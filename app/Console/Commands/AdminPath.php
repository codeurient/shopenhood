<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AdminPath extends Command
{
    protected $signature = 'admin:path
                            {--show    : Show the current admin panel path}
                            {--set=    : Set a custom admin path}
                            {--generate : Generate and save a new random path}
                            {--reset   : Remove DB-managed path (fall back to ADMIN_PATH in .env)}';

    protected $description = 'View or manage the admin panel access path (recovery tool)';

    public function handle(): int
    {
        if ($this->option('reset')) {
            return $this->resetPath();
        }

        if ($this->option('generate')) {
            return $this->generatePath();
        }

        if ($set = $this->option('set')) {
            return $this->setPath($set);
        }

        // Default: show current path
        return $this->showPath();
    }

    private function showPath(): int
    {
        $currentPath = config('admin.path');
        $source = $this->resolveSource();

        $this->info('');
        $this->line('  <fg=yellow>Admin Panel Access Path</fg=yellow>');
        $this->line('  ─────────────────────────────────────');
        $this->line("  Path   : <fg=green>{$currentPath}</fg=green>");
        $this->line("  Source : {$source}");
        $this->line('  URL    : '.url($currentPath.'/login'));
        $this->info('');

        return self::SUCCESS;
    }

    private function setPath(string $path): int
    {
        if (strlen($path) < 8) {
            $this->error('Path must be at least 8 characters.');

            return self::FAILURE;
        }

        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $path)) {
            $this->error('Path may only contain letters, numbers, hyphens, and underscores.');

            return self::FAILURE;
        }

        $this->save($path);

        $this->info("Admin path set to: {$path}");
        $this->line('Login URL: '.url($path.'/login'));

        return self::SUCCESS;
    }

    private function generatePath(): int
    {
        $path = substr(
            str_replace(['+', '/', '='], ['a', 'b', 'c'], base64_encode(random_bytes(12))),
            0,
            16
        );

        $this->save($path);

        $this->info("New admin path generated: {$path}");
        $this->line('Login URL: '.url($path.'/login'));
        $this->warn('Bookmark this URL. The previous path no longer works.');

        return self::SUCCESS;
    }

    private function resetPath(): int
    {
        Setting::query()->where('key', 'admin.access_path')->delete();
        Cache::forget('admin.access_path');

        $envPath = env('ADMIN_PATH', 'admin');

        $this->info("DB-managed path removed. Using ADMIN_PATH from .env: {$envPath}");
        $this->line('Login URL: '.url($envPath.'/login'));

        return self::SUCCESS;
    }

    private function save(string $path): void
    {
        Setting::setValue('admin.access_path', encrypt($path), 'string', 'admin');
        Cache::forget('admin.access_path');
    }

    private function resolveSource(): string
    {
        if (config('admin.path_override')) {
            return 'ADMIN_PATH_OVERRIDE (env — hard override)';
        }

        if (Setting::getValue('admin.access_path', null)) {
            return 'Database (dynamic, encrypted)';
        }

        return 'ADMIN_PATH (env)';
    }
}
