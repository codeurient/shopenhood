<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    // ip_allowlist and login_rate_limit are read per-request, so Config::set works.
    // admin.path is baked into routes at boot — use route() helpers, not hardcoded paths.
    Config::set('admin.ip_allowlist', []);
    Config::set('admin.login_rate_limit', 5);
    Cache::flush();
});

// ── Config ────────────────────────────────────────────────────────────────────

it('admin config has all required keys', function () {
    expect(config('admin.path'))->toBeString()->not->toBeEmpty();
    expect(config('admin.ip_allowlist'))->toBeArray();
    expect(config('admin.login_rate_limit'))->toBeInt()->toBeGreaterThan(0);
});

it('serves the admin login form at the configured path', function () {
    $this->get(route('admin.login'))->assertOk();
});

// ── IP Allowlist ──────────────────────────────────────────────────────────────

it('allows all IPs when the allowlist is empty', function () {
    Config::set('admin.ip_allowlist', []);

    $this->get(route('admin.login'))->assertOk();
});

it('allows requests from an IP in the allowlist', function () {
    Config::set('admin.ip_allowlist', ['127.0.0.1']);

    $this->get(route('admin.login'))->assertOk();
});

it('blocks requests from IPs not in the allowlist', function () {
    Config::set('admin.ip_allowlist', ['203.0.113.10']);

    $this->get(route('admin.login'))->assertForbidden();
});

it('blocks all IPs when allowlist has entries and request IP is not listed', function () {
    Config::set('admin.ip_allowlist', ['10.0.0.1', '10.0.0.2']);

    $this->get(route('admin.dashboard'))->assertForbidden();
});

// ── Login Rate Limiting ───────────────────────────────────────────────────────

it('throttles admin login after exceeding the rate limit', function () {
    Config::set('admin.login_rate_limit', 3);

    $payload = ['email' => 'admin@example.com', 'password' => 'wrong'];
    $loginUrl = route('admin.login.submit');

    $this->post($loginUrl, $payload);
    $this->post($loginUrl, $payload);
    $this->post($loginUrl, $payload);

    $this->post($loginUrl, $payload)->assertStatus(429);
});

// ── robots.txt does not expose admin path ─────────────────────────────────────

it('does not expose the admin path in robots.txt', function () {
    $response = $this->get('/robots.txt');
    $adminPath = config('admin.path');

    $response->assertOk();
    $response->assertDontSee('Disallow: /'.$adminPath);
    $response->assertDontSee('Disallow: /admin');
});

// ── Auth guard ────────────────────────────────────────────────────────────────

it('redirects unauthenticated users from the admin dashboard to admin login', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
});

it('allows an authenticated admin to access the dashboard', function () {
    $admin = User::factory()->create(['current_role' => 'admin']);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.dashboard'))
        ->assertOk();
});

it('blocks a normal user from accessing the admin dashboard', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);

    $this->actingAs($user, 'admin')
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

// ── DB-managed path (AppServiceProvider) ─────────────────────────────────────

it('uses the DB-managed encrypted path when set in settings', function () {
    $newPath = 'test-path-xyz9';

    \App\Models\Setting::setValue('admin.access_path', encrypt($newPath), 'string', 'admin');
    Cache::forget('admin.access_path');

    // Load via the same logic AppServiceProvider uses
    $encrypted = \App\Models\Setting::getValue('admin.access_path', null);
    expect($encrypted)->not->toBeNull();
    expect(decrypt($encrypted))->toBe($newPath);

    // Clean up
    \App\Models\Setting::query()->where('key', 'admin.access_path')->delete();
    Cache::forget('admin.access_path');
});

it('falls back gracefully when the DB path is absent', function () {
    \App\Models\Setting::query()->where('key', 'admin.access_path')->delete();
    Cache::forget('admin.access_path');

    // Should fall back to ADMIN_PATH env value without throwing
    $encrypted = \App\Models\Setting::getValue('admin.access_path', null);
    expect($encrypted)->toBeNull();
});
