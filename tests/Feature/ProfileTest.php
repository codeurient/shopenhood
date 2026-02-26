<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

// ==========================================
// AVATAR UPLOAD
// ==========================================

test('profile page shows avatar section for all users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Profile Picture');
});

test('user can upload an avatar', function () {
    Storage::fake('public');

    $user = User::factory()->create(['avatar' => null]);
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

    $response = $this->actingAs($user)
        ->patch(route('profile.avatar'), ['avatar' => $file]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();
    expect($user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

test('uploading a new avatar replaces the old one', function () {
    Storage::fake('public');

    $oldFile = UploadedFile::fake()->image('old.jpg');
    $oldPath = $oldFile->store('avatars', 'public');

    $user = User::factory()->create(['avatar' => $oldPath]);
    $newFile = UploadedFile::fake()->image('new.jpg');

    $this->actingAs($user)
        ->patch(route('profile.avatar'), ['avatar' => $newFile]);

    $user->refresh();
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($user->avatar);
});

test('avatar upload requires a file', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.avatar'), []);

    $response->assertSessionHasErrors('avatar');
});

test('avatar must be an image', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.avatar'), ['avatar' => $file]);

    $response->assertSessionHasErrors('avatar');
});

test('avatar cannot exceed 2MB', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('big.jpg')->size(3000);

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.avatar'), ['avatar' => $file]);

    $response->assertSessionHasErrors('avatar');
});

test('guest cannot upload avatar', function () {
    $this->patch(route('profile.avatar'), [])
        ->assertRedirect(route('login'));
});

// ==========================================
// BANNER UPLOAD
// ==========================================

test('profile page shows banner section for all users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Banner Image');
});

test('user can upload a banner', function () {
    Storage::fake('public');

    $user = User::factory()->create(['banner' => null]);
    $file = UploadedFile::fake()->image('banner.jpg', 1200, 300);

    $response = $this->actingAs($user)
        ->patch(route('profile.banner'), ['banner' => $file]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();
    expect($user->banner)->not->toBeNull();
    Storage::disk('public')->assertExists($user->banner);
});

test('uploading a new banner replaces the old one', function () {
    Storage::fake('public');

    $oldFile = UploadedFile::fake()->image('old-banner.jpg');
    $oldPath = $oldFile->store('banners', 'public');

    $user = User::factory()->create(['banner' => $oldPath]);
    $newFile = UploadedFile::fake()->image('new-banner.jpg');

    $this->actingAs($user)
        ->patch(route('profile.banner'), ['banner' => $newFile]);

    $user->refresh();
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($user->banner);
});

test('banner upload requires a file', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.banner'), [])
        ->assertSessionHasErrors('banner');
});

test('banner must be an image', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.banner'), ['banner' => $file])
        ->assertSessionHasErrors('banner');
});

test('banner cannot exceed 4MB', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('big-banner.jpg')->size(5000);

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.banner'), ['banner' => $file])
        ->assertSessionHasErrors('banner');
});

test('guest cannot upload banner', function () {
    $this->patch(route('profile.banner'), [])
        ->assertRedirect(route('login'));
});
