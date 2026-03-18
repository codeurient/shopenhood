<?php

use App\Models\LoginHistory;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can view login histories index', function () {
    $user = User::factory()->create();
    LoginHistory::create([
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'logged_in_at' => now(),
    ]);

    $response = $this->get(route('admin.login-histories.index'));

    $response->assertSuccessful();
    $response->assertSee('Login Histories');
});

test('admin can clear all login histories', function () {
    $user = User::factory()->create();
    LoginHistory::create([
        'user_id' => $user->id,
        'ip_address' => '10.0.0.1',
        'logged_in_at' => now(),
    ]);
    LoginHistory::create([
        'user_id' => $user->id,
        'ip_address' => '10.0.0.2',
        'logged_in_at' => now(),
    ]);

    expect(LoginHistory::count())->toBe(2);

    $response = $this->delete(route('admin.login-histories.clear-all'));

    $response->assertRedirect(route('admin.login-histories.index'));
    $response->assertSessionHas('success');
    expect(LoginHistory::count())->toBe(0);
});

test('clear all login histories shows count in success message', function () {
    $user = User::factory()->create();
    LoginHistory::create([
        'user_id' => $user->id,
        'ip_address' => '192.168.1.1',
        'logged_in_at' => now(),
    ]);

    $response = $this->delete(route('admin.login-histories.clear-all'));

    $response->assertSessionHas('success', fn ($msg) => str_contains($msg, '1'));
});

test('clear all login histories on empty table returns success', function () {
    $response = $this->delete(route('admin.login-histories.clear-all'));

    $response->assertRedirect(route('admin.login-histories.index'));
    $response->assertSessionHas('success');
    expect(LoginHistory::count())->toBe(0);
});
