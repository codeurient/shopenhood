<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('new users can register with phone number', function () {
    $response = $this->post('/register', [
        'name' => 'Phone User',
        'email' => 'phone@example.com',
        'phone' => '+1234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);

    $user = User::where('email', 'phone@example.com')->first();
    expect($user->phone)->toBe('+1234567890');
    expect($user->current_role)->toBe('normal_user');
});

test('new users can register without phone number', function () {
    $response = $this->post('/register', [
        'name' => 'No Phone User',
        'email' => 'nophone@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);

    $user = User::where('email', 'nophone@example.com')->first();
    expect($user->phone)->toBeNull();
});
