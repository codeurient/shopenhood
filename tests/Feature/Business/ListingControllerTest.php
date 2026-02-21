<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\User;

beforeEach(function () {
    $this->businessUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $this->actingAs($this->businessUser);

    $this->category = Category::factory()->create();
    $this->listingType = ListingType::factory()->create();
});

test('non-business user is redirected to upgrade page', function () {
    $normalUser = User::factory()->create(['current_role' => 'normal_user']);
    $this->actingAs($normalUser);

    $response = $this->get(route('business.listings.index'));

    $response->assertRedirect(route('business.upgrade'));
});

test('non-business user cannot post to business store', function () {
    $normalUser = User::factory()->create(['current_role' => 'normal_user']);
    $this->actingAs($normalUser);

    $response = $this->post(route('business.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Attempted Business Listing',
        'description' => 'Should not work',
        'condition' => 'new',
    ]);

    $response->assertRedirect(route('business.upgrade'));
});

test('business user can view business listings index', function () {
    $response = $this->get(route('business.listings.index'));

    $response->assertSuccessful();
});

test('business user can view create listing form', function () {
    $response = $this->get(route('business.listings.create'));

    $response->assertSuccessful();
});

test('business user cannot access create when at limit', function () {
    $limitedUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'listing_limit' => 1,
    ]);
    $this->actingAs($limitedUser);

    Listing::factory()->create([
        'user_id' => $limitedUser->id,
        'listing_mode' => 'business',
    ]);

    $response = $this->get(route('business.listings.create'));

    $response->assertRedirect(route('business.listings.index'));
    $response->assertSessionHas('error');
});

test('business user can create a listing', function () {
    $response = $this->post(route('business.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'My Business Listing',
        'description' => 'A description for my business listing',
        'condition' => 'new',
    ]);

    $response->assertRedirect(route('business.listings.index'));

    $listing = Listing::where('user_id', $this->businessUser->id)->first();
    expect($listing)->not->toBeNull();
    expect($listing->title)->toBe('My Business Listing');
    expect($listing->status)->toBe('pending');
    expect($listing->listing_mode)->toBe('business');
    expect($listing->expires_at)->not->toBeNull();
});

test('business user cannot create listing when at limit via store', function () {
    $limitedUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
        'listing_limit' => 1,
    ]);
    $this->actingAs($limitedUser);

    Listing::factory()->create([
        'user_id' => $limitedUser->id,
        'listing_mode' => 'business',
    ]);

    $response = $this->post(route('business.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Over Limit',
        'description' => 'Should be blocked',
        'condition' => 'new',
    ]);

    $response->assertRedirect(route('business.listings.index'));
    $response->assertSessionHas('error');
});

test('business user can create listing with store name and SEO', function () {
    $response = $this->post(route('business.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'SEO Listing',
        'description' => 'Description',
        'condition' => 'new',
        'store_name' => 'My Awesome Store',
        'meta_title' => 'SEO Title Here',
        'meta_description' => 'SEO description for the listing',
    ]);

    $response->assertRedirect(route('business.listings.index'));

    $listing = Listing::where('user_id', $this->businessUser->id)->first();
    expect($listing->store_name)->toBe('My Awesome Store');
    expect($listing->meta_title)->toBe('SEO Title Here');
    expect($listing->meta_description)->toBe('SEO description for the listing');
});

test('business user can create listing with availability type', function () {
    $response = $this->post(route('business.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Available By Order Item',
        'description' => 'An on-demand listing',
        'condition' => 'new',
        'availability_type' => 'available_by_order',
    ]);

    $response->assertRedirect(route('business.listings.index'));

    $listing = Listing::where('user_id', $this->businessUser->id)->first();
    expect($listing->availability_type)->toBe('available_by_order');
});

test('business user can edit own business listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'category_id' => $this->category->id,
        'listing_mode' => 'business',
    ]);

    $response = $this->get(route('business.listings.edit', $listing));

    $response->assertSuccessful();
});

test('business user cannot edit another users listing', function () {
    $otherUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $listing = Listing::factory()->create([
        'user_id' => $otherUser->id,
        'listing_mode' => 'business',
    ]);

    $response = $this->get(route('business.listings.edit', $listing));

    $response->assertForbidden();
});

test('business user cannot edit a normal mode listing via business route', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'listing_mode' => 'normal',
    ]);

    $response = $this->get(route('business.listings.edit', $listing));

    $response->assertForbidden();
});

test('business user can update own business listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
        'listing_mode' => 'business',
    ]);

    $response = $this->put(route('business.listings.update', $listing), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Updated Business Title',
        'description' => 'Updated description',
        'condition' => 'new',
    ]);

    $response->assertRedirect(route('business.listings.index'));

    $listing->refresh();
    expect($listing->title)->toBe('Updated Business Title');
    expect($listing->status)->toBe('pending');
});

test('business user can toggle listing visibility', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'listing_mode' => 'business',
        'is_visible' => true,
    ]);

    $response = $this->patch(route('business.listings.toggle', $listing));

    $response->assertRedirect();

    $listing->refresh();
    expect($listing->is_visible)->toBeFalse();
});

test('business user can soft delete own listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'listing_mode' => 'business',
    ]);

    $response = $this->delete(route('business.listings.destroy', $listing));

    $response->assertRedirect(route('business.listings.index'));
    expect(Listing::find($listing->id))->toBeNull();
    expect(Listing::withTrashed()->find($listing->id))->not->toBeNull();
});

test('business user can reshare a trashed listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'listing_mode' => 'business',
    ]);
    $listing->delete();

    $response = $this->post(route('business.listings.reshare', $listing->id));

    $response->assertRedirect(route('business.listings.index'));

    $listing->refresh();
    expect($listing->trashed())->toBeFalse();
    expect($listing->status)->toBe('pending');
});

test('business user can force delete trashed listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'listing_mode' => 'business',
    ]);
    $listing->delete();

    $response = $this->delete(route('business.listings.force-destroy', $listing->id));

    $response->assertRedirect(route('business.listings.index'));
    expect(Listing::withTrashed()->find($listing->id))->toBeNull();
});

test('business index only shows business mode listings', function () {
    Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'title' => 'Business Mode Listing',
        'listing_mode' => 'business',
    ]);

    Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'title' => 'Normal Mode Hidden',
        'listing_mode' => 'normal',
    ]);

    $response = $this->get(route('business.listings.index'));

    $response->assertSuccessful();
    $response->assertSee('Business Mode Listing');
    $response->assertDontSee('Normal Mode Hidden');
});

test('business user can create listing with product variations', function () {
    $response = $this->post(route('business.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Listing With Variations',
        'description' => 'Description',
        'condition' => 'new',
        'variations' => [
            [
                'sku' => 'VAR-001',
                'price' => 29.99,
                'stock_quantity' => 100,
                'is_default' => 1,
                'is_active' => 1,
            ],
            [
                'sku' => 'VAR-002',
                'price' => 39.99,
                'stock_quantity' => 50,
                'is_default' => 0,
                'is_active' => 1,
            ],
        ],
    ]);

    $response->assertRedirect(route('business.listings.index'));

    $listing = Listing::where('user_id', $this->businessUser->id)->first();
    expect($listing)->not->toBeNull();
    expect($listing->variations)->toHaveCount(2);
    expect($listing->variations->first()->sku)->toBe('VAR-001');
});
