<?php

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

function createNotification(User $user, bool $read = false): DatabaseNotification
{
    return DatabaseNotification::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\ListingApproved',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => ['type' => 'listing_approved', 'message' => 'Your listing was approved.'],
        'read_at' => $read ? now() : null,
    ]);
}

it('shows the notifications index', function () {
    createNotification($this->user);

    $this->get(route('user.notifications.index'))
        ->assertOk()
        ->assertSee('Notifications');
});

it('marks all as read on index visit', function () {
    createNotification($this->user, read: false);

    $this->get(route('user.notifications.index'));

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

it('marks all as read via post', function () {
    createNotification($this->user, read: false);

    $this->post(route('user.notifications.read-all'))
        ->assertRedirect();

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

it('deletes a single notification', function () {
    $notification = createNotification($this->user);

    $this->delete(route('user.notifications.destroy', $notification->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($this->user->notifications()->where('id', $notification->id)->exists())->toBeFalse();
});

it('cannot delete another user\'s notification', function () {
    $other = User::factory()->create();
    $notification = createNotification($other);

    $this->delete(route('user.notifications.destroy', $notification->id))
        ->assertRedirect();

    expect(DatabaseNotification::where('id', $notification->id)->exists())->toBeTrue();
});

it('deletes all notifications', function () {
    createNotification($this->user);
    createNotification($this->user);
    createNotification($this->user, read: true);

    $this->delete(route('user.notifications.delete-all'))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($this->user->notifications()->count())->toBe(0);
});

it('delete all only affects the authenticated user\'s notifications', function () {
    $other = User::factory()->create();
    createNotification($this->user);
    createNotification($other);

    $this->delete(route('user.notifications.delete-all'));

    expect($this->user->notifications()->count())->toBe(0)
        ->and(DatabaseNotification::where('notifiable_id', $other->id)->count())->toBe(1);
});

it('requires auth for all notification routes', function () {
    auth()->logout();

    $this->get(route('user.notifications.index'))->assertRedirect(route('login'));
    $this->post(route('user.notifications.read-all'))->assertRedirect(route('login'));
    $this->delete(route('user.notifications.delete-all'))->assertRedirect(route('login'));
    $this->delete(route('user.notifications.destroy', 'fake-id'))->assertRedirect(route('login'));
});
