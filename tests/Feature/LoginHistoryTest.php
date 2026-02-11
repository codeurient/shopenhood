<?php

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

test('login event creates login history record', function () {
    $user = User::factory()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertDatabaseHas('login_histories', [
        'user_id' => $user->id,
    ]);
});

test('login history captures ip address', function () {
    $user = User::factory()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $history = LoginHistory::where('user_id', $user->id)->first();

    expect($history)->not->toBeNull();
    expect($history->ip_address)->not->toBeNull();
});

test('login history captures device info', function () {
    $user = User::factory()->create();

    $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0',
    ])->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $history = LoginHistory::where('user_id', $user->id)->first();

    expect($history->device)->toBe('Desktop');
    expect($history->browser)->toBe('Chrome');
    expect($history->platform)->toBe('Windows');
});

test('first login is not marked as suspicious', function () {
    $user = User::factory()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $history = LoginHistory::where('user_id', $user->id)->first();

    expect($history->is_suspicious)->toBeFalse();
});

test('user has login histories relationship', function () {
    $user = User::factory()->create();

    LoginHistory::create([
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'logged_in_at' => now(),
    ]);

    expect($user->loginHistories)->toHaveCount(1);
});

test('admin can view login histories index', function () {
    $admin = User::factory()->create(['current_role' => 'admin']);

    LoginHistory::create([
        'user_id' => $admin->id,
        'ip_address' => '192.168.1.1',
        'logged_in_at' => now(),
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.login-histories.index'))
        ->assertSuccessful();
});

test('admin can view single login history', function () {
    $admin = User::factory()->create(['current_role' => 'admin']);

    $history = LoginHistory::create([
        'user_id' => $admin->id,
        'ip_address' => '192.168.1.1',
        'logged_in_at' => now(),
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.login-histories.show', $history))
        ->assertSuccessful();
});

test('admin can view user login history', function () {
    $admin = User::factory()->create(['current_role' => 'admin']);
    $user = User::factory()->create();

    LoginHistory::create([
        'user_id' => $user->id,
        'ip_address' => '10.0.0.1',
        'logged_in_at' => now(),
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.login-histories.user', $user))
        ->assertSuccessful();
});
