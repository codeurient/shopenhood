<?php

use App\Models\Listing;
use App\Models\Setting;
use App\Models\User;
use App\Services\ListingService;

beforeEach(function () {
    $this->service = new ListingService;
});

test('normal user cannot create listing when at limit', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);
    Listing::factory()->create(['user_id' => $user->id]);

    expect($this->service->canUserCreateListing($user))->toBeFalse();
});

test('normal user can create listing when none exist', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);

    expect($this->service->canUserCreateListing($user))->toBeTrue();
});

test('business user with null limit has unlimited listings', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'listing_limit' => null,
    ]);

    Listing::factory()->count(5)->create(['user_id' => $user->id]);

    expect($this->service->canUserCreateListing($user))->toBeTrue();
});

test('business user with limit is enforced', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'listing_limit' => 3,
    ]);

    Listing::factory()->count(3)->create(['user_id' => $user->id]);

    expect($this->service->canUserCreateListing($user))->toBeFalse();
});

test('remaining slots returns correct count for normal user', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);

    expect($this->service->getRemainingListingSlots($user))->toBe(1);

    Listing::factory()->create(['user_id' => $user->id]);

    expect($this->service->getRemainingListingSlots($user))->toBe(0);
});

test('remaining slots returns null for unlimited business user', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'listing_limit' => null,
    ]);

    expect($this->service->getRemainingListingSlots($user))->toBeNull();
});

test('deactivated listing still counts toward limit', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);
    Listing::factory()->create(['user_id' => $user->id, 'is_visible' => false]);

    expect($this->service->canUserCreateListing($user))->toBeFalse();
});

test('trashed listing does not count toward limit', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);
    $listing = Listing::factory()->create(['user_id' => $user->id]);
    $listing->delete();

    expect($this->service->canUserCreateListing($user))->toBeTrue();
});

test('expire overdue listings soft deletes them', function () {
    $listing = Listing::factory()->create([
        'status' => 'active',
        'expires_at' => now()->subDay(),
    ]);

    $count = $this->service->expireOverdueListings();

    expect($count)->toBe(1);

    $listing->refresh();
    expect($listing->trashed())->toBeTrue();
    expect($listing->status)->toBe('expired');
});

test('expire does not affect listings without expiration', function () {
    Listing::factory()->create([
        'status' => 'active',
        'expires_at' => null,
    ]);

    $count = $this->service->expireOverdueListings();

    expect($count)->toBe(0);
});

test('purge deletes old soft-deleted listings', function () {
    Setting::setValue('listing.soft_delete_retention_days', 30, 'integer', 'listing');

    $listing = Listing::factory()->create();
    $listing->delete();

    // Manually set deleted_at to 31 days ago
    Listing::withTrashed()
        ->where('id', $listing->id)
        ->update(['deleted_at' => now()->subDays(31)]);

    $count = $this->service->purgeOldDeletedListings();

    expect($count)->toBe(1);
    expect(Listing::withTrashed()->find($listing->id))->toBeNull();
});

test('purge does not delete recently trashed listings', function () {
    Setting::setValue('listing.soft_delete_retention_days', 30, 'integer', 'listing');

    $listing = Listing::factory()->create();
    $listing->delete();

    $count = $this->service->purgeOldDeletedListings();

    expect($count)->toBe(0);
    expect(Listing::withTrashed()->find($listing->id))->not->toBeNull();
});

test('can reshare a trashed listing', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);
    $listing = Listing::factory()->create(['user_id' => $user->id]);
    $listing->delete();

    expect($this->service->canReshareListing($user, $listing))->toBeTrue();

    $restored = $this->service->reshareListing($user, $listing);

    expect($restored->trashed())->toBeFalse();
    expect($restored->status)->toBe('pending');
    expect($restored->expires_at)->not->toBeNull();
});

test('cannot reshare when at listing limit', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);

    $listing1 = Listing::factory()->create(['user_id' => $user->id]);
    $listing1->delete();

    // Create another active listing to fill the slot
    Listing::factory()->create(['user_id' => $user->id]);

    expect($this->service->canReshareListing($user, $listing1))->toBeFalse();
});

test('slug availability checks trashed listings', function () {
    $listing = Listing::factory()->create(['slug' => 'test-slug']);
    $listing->delete();

    expect($this->service->isSlugAvailable('test-slug'))->toBeFalse();
    expect($this->service->isSlugAvailable('test-slug', $listing->id))->toBeTrue();
    expect($this->service->isSlugAvailable('other-slug'))->toBeTrue();
});

test('get default duration reads from settings', function () {
    Setting::setValue('listing.default_duration_days', 45, 'integer', 'listing');

    expect($this->service->getDefaultDurationDays())->toBe(45);
});

test('calculate expires at uses default duration', function () {
    Setting::setValue('listing.default_duration_days', 30, 'integer', 'listing');

    $expiresAt = $this->service->calculateExpiresAt();

    expect($expiresAt->diffInDays(now()))->toBeBetween(29, 31);
});

test('calculate expires at accepts custom duration', function () {
    $expiresAt = $this->service->calculateExpiresAt(7);

    expect($expiresAt->diffInDays(now()))->toBeBetween(6, 8);
});
