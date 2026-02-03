<?php

use App\Models\Listing;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can view users index', function () {
    User::factory()->count(3)->create();

    $response = $this->get(route('admin.users.index'));

    $response->assertSuccessful();
});

test('admin can search users', function () {
    User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

    $response = $this->get(route('admin.users.index', ['search' => 'John']));

    $response->assertSuccessful();
    $response->assertSee('John Doe');
});

test('admin can filter users by role', function () {
    User::factory()->create(['current_role' => 'business_user']);

    $response = $this->get(route('admin.users.index', ['role' => 'business_user']));

    $response->assertSuccessful();
});

test('admin can view user edit page', function () {
    $user = User::factory()->create();

    $response = $this->get(route('admin.users.edit', $user));

    $response->assertSuccessful();
    $response->assertSee($user->name);
});

test('admin can update user role', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);

    $response = $this->put(route('admin.users.update', $user), [
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'listing_limit' => null,
        'business_valid_until' => null,
        'status' => 'active',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->current_role)->toBe('business_user');
    expect($user->is_business_enabled)->toBeTrue();
});

test('admin can set business user limit and validity', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);
    $validUntil = now()->addYear()->format('Y-m-d');

    $response = $this->put(route('admin.users.update', $user), [
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'listing_limit' => 10,
        'business_valid_until' => $validUntil,
        'status' => 'active',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->listing_limit)->toBe(10);
    expect($user->business_valid_until->format('Y-m-d'))->toBe($validUntil);
});

test('admin can suspend a user', function () {
    $user = User::factory()->create(['current_role' => 'normal_user', 'status' => 'active']);

    $response = $this->put(route('admin.users.update', $user), [
        'current_role' => 'normal_user',
        'status' => 'suspended',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->status)->toBe('suspended');
});

test('admin can soft delete a user', function () {
    $user = User::factory()->create();

    $response = $this->delete(route('admin.users.destroy', $user));

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success');

    expect(User::find($user->id))->toBeNull();
    expect(User::withTrashed()->find($user->id))->not->toBeNull();
});

test('admin cannot delete their own account', function () {
    $response = $this->delete(route('admin.users.destroy', $this->admin));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect(User::find($this->admin->id))->not->toBeNull();
});

test('admin can view settings page', function () {
    $response = $this->get(route('admin.settings.index'));

    $response->assertSuccessful();
});

test('admin can update settings', function () {
    $response = $this->put(route('admin.settings.update'), [
        'listing_default_duration_days' => 60,
        'listing_soft_delete_retention_days' => 45,
    ]);

    $response->assertRedirect(route('admin.settings.index'));

    expect(\App\Models\Setting::getValue('listing.default_duration_days'))->toBe(60);
    expect(\App\Models\Setting::getValue('listing.soft_delete_retention_days'))->toBe(45);
});

test('settings validation rejects invalid values', function () {
    $response = $this->put(route('admin.settings.update'), [
        'listing_default_duration_days' => 0,
        'listing_soft_delete_retention_days' => 500,
    ]);

    $response->assertSessionHasErrors(['listing_default_duration_days', 'listing_soft_delete_retention_days']);
});

// ==========================================
// ROLE CHANGE LISTING MANAGEMENT
// ==========================================

test('downgrade from business to normal hides excess listings', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $oldest = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'created_at' => now()->subDays(3),
    ]);
    $middle = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'created_at' => now()->subDays(2),
    ]);
    $newest = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'created_at' => now()->subDay(),
    ]);

    $this->put(route('admin.users.update', $user), [
        'current_role' => 'normal_user',
        'status' => 'active',
    ]);

    $oldest->refresh();
    $middle->refresh();
    $newest->refresh();

    expect($newest->hidden_due_to_role_change)->toBeFalse();
    expect($oldest->hidden_due_to_role_change)->toBeTrue();
    expect($middle->hidden_due_to_role_change)->toBeTrue();
});

test('downgrade keeps most recent listing active', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $older = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'created_at' => now()->subDays(2),
    ]);
    $newer = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'created_at' => now()->subDay(),
    ]);

    $this->put(route('admin.users.update', $user), [
        'current_role' => 'normal_user',
        'status' => 'active',
    ]);

    $newer->refresh();
    expect($newer->hidden_due_to_role_change)->toBeFalse();
    expect($newer->status)->toBe('active');
});

test('upgrade from normal to business clears role restriction', function () {
    $user = User::factory()->create([
        'current_role' => 'normal_user',
    ]);

    $listing1 = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'hidden_due_to_role_change' => true,
    ]);
    $listing2 = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'hidden_due_to_role_change' => true,
    ]);

    $this->put(route('admin.users.update', $user), [
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'status' => 'active',
    ]);

    $listing1->refresh();
    $listing2->refresh();

    expect($listing1->hidden_due_to_role_change)->toBeFalse();
    expect($listing2->hidden_due_to_role_change)->toBeFalse();
});

test('downgrade with only one listing does not hide it', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $listing = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $this->put(route('admin.users.update', $user), [
        'current_role' => 'normal_user',
        'status' => 'active',
    ]);

    $listing->refresh();
    expect($listing->hidden_due_to_role_change)->toBeFalse();
});

test('downgrade with no active listings does nothing', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $pending = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    $this->put(route('admin.users.update', $user), [
        'current_role' => 'normal_user',
        'status' => 'active',
    ]);

    $pending->refresh();
    expect($pending->hidden_due_to_role_change)->toBeFalse();
});

test('upgrade clears role restriction on trashed listings too', function () {
    $user = User::factory()->create([
        'current_role' => 'normal_user',
    ]);

    $listing = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'hidden_due_to_role_change' => true,
    ]);
    $listing->delete();

    $this->put(route('admin.users.update', $user), [
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'status' => 'active',
    ]);

    $listing->refresh();
    expect($listing->hidden_due_to_role_change)->toBeFalse();
});

test('publicly visible scope excludes role restricted listings', function () {
    $user = User::factory()->create();

    $visible = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
    ]);
    $hidden = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => true,
    ]);

    $results = Listing::publiclyVisible()->get();

    expect($results->pluck('id'))->toContain($visible->id);
    expect($results->pluck('id'))->not->toContain($hidden->id);
});
