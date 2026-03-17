<?php

use App\Exceptions\Handler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

it('redirects to home on normal logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');
});

it('does not log out the admin guard when a normal user logs out', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['current_role' => 'admin']);

    $this->actingAs($admin, 'admin');
    $this->actingAs($user, 'web');

    $this->post('/logout');

    expect(auth('admin')->check())->toBeTrue()
        ->and(auth('web')->check())->toBeFalse();
});

it('does not log out the web guard when the admin logs out', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['current_role' => 'admin']);

    $this->actingAs($user, 'web');
    $this->actingAs($admin, 'admin');

    $this->post('/admin/logout');

    expect(auth('web')->check())->toBeTrue()
        ->and(auth('admin')->check())->toBeFalse();
});

it('redirects to login when a token mismatch occurs on the logout route', function () {
    $request = Request::create('/logout', 'POST');
    $exception = new TokenMismatchException;

    /** @var Handler $handler */
    $handler = app(Handler::class);
    $response = $handler->render($request, $exception);

    expect($response->headers->get('Location'))->toBe(route('login'));
});

it('redirects admin to admin login when a token mismatch occurs on the admin logout route', function () {
    $request = Request::create('/admin/logout', 'POST');
    $exception = new TokenMismatchException;

    /** @var Handler $handler */
    $handler = app(Handler::class);
    $response = $handler->render($request, $exception);

    expect($response->headers->get('Location'))->toBe(route('admin.login'));
});
