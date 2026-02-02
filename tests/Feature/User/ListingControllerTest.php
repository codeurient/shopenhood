<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Setting;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['current_role' => 'normal_user']);
    $this->actingAs($this->user);

    $this->category = Category::factory()->create();
    $this->listingType = ListingType::factory()->create();
});

test('user can view my listings page', function () {
    $response = $this->get(route('user.listings.index'));

    $response->assertSuccessful();
});

test('user can view create listing form', function () {
    $response = $this->get(route('user.listings.create'));

    $response->assertSuccessful();
});

test('user cannot access create when at limit', function () {
    Listing::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('user.listings.create'));

    $response->assertRedirect(route('user.listings.index'));
    $response->assertSessionHas('error');
});

test('user can create a listing', function () {
    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'My Test Listing',
        'description' => 'A description for my listing',
        'base_price' => 99.99,
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $this->user->id)->first();
    expect($listing)->not->toBeNull();
    expect($listing->title)->toBe('My Test Listing');
    expect($listing->status)->toBe('pending');
    expect($listing->created_as_role)->toBe('normal_user');
    expect($listing->expires_at)->not->toBeNull();
});

test('user cannot create listing when at limit via store', function () {
    Listing::factory()->create(['user_id' => $this->user->id]);

    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Another Listing',
        'description' => 'Description',
    ]);

    $response->assertRedirect(route('user.listings.index'));
    $response->assertSessionHas('error');
});

test('user can edit own listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
    ]);

    $response = $this->get(route('user.listings.edit', $listing));

    $response->assertSuccessful();
});

test('user cannot edit another users listing', function () {
    $otherUser = User::factory()->create();
    $listing = Listing::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->get(route('user.listings.edit', $listing));

    $response->assertForbidden();
});

test('user can update own listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
    ]);

    $response = $this->put(route('user.listings.update', $listing), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Updated Title',
        'description' => 'Updated description',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing->refresh();
    expect($listing->title)->toBe('Updated Title');
    expect($listing->status)->toBe('pending');
});

test('user can toggle listing visibility', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'is_visible' => true,
    ]);

    $response = $this->patch(route('user.listings.toggle', $listing));

    $response->assertRedirect();

    $listing->refresh();
    expect($listing->is_visible)->toBeFalse();
});

test('user can soft delete own listing', function () {
    $listing = Listing::factory()->create(['user_id' => $this->user->id]);

    $response = $this->delete(route('user.listings.destroy', $listing));

    $response->assertRedirect(route('user.listings.index'));
    expect(Listing::find($listing->id))->toBeNull();
    expect(Listing::withTrashed()->find($listing->id))->not->toBeNull();
});

test('user can reshare a trashed listing', function () {
    $listing = Listing::factory()->create(['user_id' => $this->user->id]);
    $listing->delete();

    $response = $this->post(route('user.listings.reshare', $listing));

    $response->assertRedirect(route('user.listings.index'));

    $listing->refresh();
    expect($listing->trashed())->toBeFalse();
    expect($listing->status)->toBe('pending');
});

test('normal user cannot force delete', function () {
    $listing = Listing::factory()->create(['user_id' => $this->user->id]);
    $listing->delete();

    $response = $this->delete(route('user.listings.force-destroy', $listing));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect(Listing::withTrashed()->find($listing->id))->not->toBeNull();
});

test('business user can force delete trashed listing', function () {
    $businessUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $this->actingAs($businessUser);

    $listing = Listing::factory()->create(['user_id' => $businessUser->id]);
    $listing->delete();

    $response = $this->delete(route('user.listings.force-destroy', $listing));

    $response->assertRedirect(route('user.listings.index'));
    expect(Listing::withTrashed()->find($listing->id))->toBeNull();
});

test('trashed listings show on index page', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'title' => 'Trashed Listing',
    ]);
    $listing->delete();

    $response = $this->get(route('user.listings.index'));

    $response->assertSuccessful();
    $response->assertSee('Trashed Listing');
});

test('expire command works', function () {
    Listing::factory()->create([
        'status' => 'active',
        'expires_at' => now()->subHour(),
    ]);

    $this->artisan('listings:expire')
        ->assertSuccessful()
        ->expectsOutputToContain('Expired 1');
});

test('purge command works', function () {
    Setting::setValue('listing.soft_delete_retention_days', 30, 'integer', 'listing');

    $listing = Listing::factory()->create();
    $listing->delete();

    Listing::withTrashed()
        ->where('id', $listing->id)
        ->update(['deleted_at' => now()->subDays(31)]);

    $this->artisan('listings:purge-deleted')
        ->assertSuccessful()
        ->expectsOutputToContain('Purged 1');
});
