<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AccessSecurityController extends Controller
{
    public function index(): View
    {
        $currentPath = config('admin.path');
        $isManagedByDb = (bool) Setting::getValue('admin.access_path', null);
        $isOverriddenByEnv = (bool) config('admin.path_override');

        return view('admin.access-security.index', compact(
            'currentPath',
            'isManagedByDb',
            'isOverriddenByEnv'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'admin_path' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'regex:/^[a-zA-Z0-9_-]+$/',
            ],
        ], [
            'admin_path.min' => 'The admin path must be at least 8 characters.',
            'admin_path.regex' => 'The admin path may only contain letters, numbers, hyphens, and underscores.',
        ]);

        $newPath = $validated['admin_path'];

        $this->savePath($newPath);

        activity()
            ->causedBy(auth('admin')->user())
            ->withProperties(['action' => 'custom_path_set'])
            ->log('Changed admin panel access path');

        // Routes for this request still use the OLD prefix.
        // Redirect to the new URL manually so the browser lands on the new path.
        return redirect(url($newPath.'/dashboard'))
            ->with('success', 'Admin path updated. Bookmark your new URL.');
    }

    public function generate(): RedirectResponse
    {
        $newPath = $this->generatePath();

        $this->savePath($newPath);

        activity()
            ->causedBy(auth('admin')->user())
            ->withProperties(['action' => 'path_generated'])
            ->log('Generated new admin panel access path');

        return redirect(url($newPath.'/dashboard'))
            ->with('success', "New path generated and activated: {$newPath}");
    }

    public function reset(): RedirectResponse
    {
        Setting::query()->where('key', 'admin.access_path')->delete();
        Cache::forget('admin.access_path');

        activity()
            ->causedBy(auth('admin')->user())
            ->withProperties(['action' => 'path_reset'])
            ->log('Reset admin panel access path to .env value');

        return redirect(route('admin.access-security.index'))
            ->with('success', 'Admin path reset. The system now uses the ADMIN_PATH value from .env.');
    }

    /** Store the new path encrypted and clear the cache. */
    private function savePath(string $path): void
    {
        Setting::setValue('admin.access_path', encrypt($path), 'string', 'admin');
        Cache::forget('admin.access_path');
    }

    /** Generate a cryptographically random, URL-safe path slug. */
    private function generatePath(): string
    {
        return substr(
            str_replace(['+', '/', '='], ['a', 'b', 'c'], base64_encode(random_bytes(12))),
            0,
            16
        );
    }
}
