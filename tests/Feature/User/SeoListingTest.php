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

    $this->normalUser = User::factory()->create([
        'current_role' => 'normal_user',
    ]);

    $this->category = Category::factory()->create(['is_active' => true]);
    $this->listingType = ListingType::factory()->create(['is_active' => true]);
});

// ==========================================
// SEO FIELD ACCESS
// ==========================================

test('business user can create listing with seo fields', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'SEO Test Product',
        'description' => 'A product with SEO optimization.',
        'condition' => 'new',
        'meta_title' => 'Premium Cotton T-Shirts Wholesale | TestBrand',
        'meta_description' => 'Shop premium cotton t-shirts in bulk. MOQ 10 pieces. Free shipping on orders over $500.',
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertRedirect(route('business.listings.index'));

    $this->assertDatabaseHas('listings', [
        'title' => 'SEO Test Product',
        'meta_title' => 'Premium Cotton T-Shirts Wholesale | TestBrand',
        'meta_description' => 'Shop premium cotton t-shirts in bulk. MOQ 10 pieces. Free shipping on orders over $500.',
    ]);
});

test('normal user cannot set seo fields on listing', function () {
    $this->actingAs($this->normalUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Normal User Product',
        'description' => 'A product from normal user.',
        'condition' => 'new',
        'base_price' => 25.00,
        'meta_title' => 'This Should Not Be Saved',
        'meta_description' => 'This description should not be saved either.',
    ];

    $response = $this->post(route('user.listings.store'), $data);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('title', 'Normal User Product')->first();
    expect($listing)->not->toBeNull();
    expect($listing->meta_title)->toBeNull();
    expect($listing->meta_description)->toBeNull();
});

test('business user can update listing with seo fields', function () {
    $this->actingAs($this->businessUser);

    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
        'listing_mode' => 'business',
        'meta_title' => null,
        'meta_description' => null,
        'status' => 'active',
    ]);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => $listing->title,
        'description' => $listing->description,
        'condition' => 'new',
        'meta_title' => 'Updated SEO Title | Brand',
        'meta_description' => 'Updated SEO description with keywords and benefits.',
    ];

    $response = $this->put(route('business.listings.update', $listing), $data);

    $response->assertRedirect(route('business.listings.index'));

    $listing->refresh();
    expect($listing->meta_title)->toBe('Updated SEO Title | Brand');
    expect($listing->meta_description)->toBe('Updated SEO description with keywords and benefits.');
});

test('normal user cannot update seo fields on listing', function () {
    $this->actingAs($this->normalUser);

    $listing = Listing::factory()->create([
        'user_id' => $this->normalUser->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
        'meta_title' => null,
        'meta_description' => null,
        'status' => 'active',
    ]);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => $listing->title,
        'description' => $listing->description,
        'condition' => 'new',
        'base_price' => 25.00,
        'meta_title' => 'Should Not Be Saved',
        'meta_description' => 'This should not be saved.',
    ];

    $response = $this->put(route('user.listings.update', $listing), $data);

    $response->assertRedirect(route('user.listings.index'));

    $listing->refresh();
    expect($listing->meta_title)->toBeNull();
    expect($listing->meta_description)->toBeNull();
});

// ==========================================
// SEO VALIDATION
// ==========================================

test('meta title cannot exceed 60 characters', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Test Product',
        'description' => 'Test description.',
        'condition' => 'new',
        'meta_title' => str_repeat('a', 61),
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertSessionHasErrors('meta_title');
});

test('meta description cannot exceed 160 characters', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Test Product',
        'description' => 'Test description.',
        'condition' => 'new',
        'meta_description' => str_repeat('a', 161),
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertSessionHasErrors('meta_description');
});

test('meta title at max length is valid', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Test Product',
        'description' => 'Test description.',
        'condition' => 'new',
        'meta_title' => str_repeat('a', 60),
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertSessionDoesntHaveErrors('meta_title');
    $response->assertRedirect(route('business.listings.index'));

    $this->assertDatabaseHas('listings', [
        'title' => 'Test Product',
        'meta_title' => str_repeat('a', 60),
    ]);
});

test('meta description at max length is valid', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Test Product',
        'description' => 'Test description.',
        'condition' => 'new',
        'meta_description' => str_repeat('a', 160),
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertSessionDoesntHaveErrors('meta_description');
    $response->assertRedirect(route('business.listings.index'));

    $this->assertDatabaseHas('listings', [
        'title' => 'Test Product',
        'meta_description' => str_repeat('a', 160),
    ]);
});

// ==========================================
// SEO FIELDS ARE OPTIONAL
// ==========================================

test('seo fields are optional for business users', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Product Without SEO',
        'description' => 'A product without any SEO fields set.',
        'condition' => 'new',
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertRedirect(route('business.listings.index'));
    $response->assertSessionDoesntHaveErrors(['meta_title', 'meta_description']);

    $this->assertDatabaseHas('listings', [
        'title' => 'Product Without SEO',
        'meta_title' => null,
        'meta_description' => null,
    ]);
});

test('business user can clear seo fields on update', function () {
    $this->actingAs($this->businessUser);

    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
        'listing_mode' => 'business',
        'meta_title' => 'Original SEO Title',
        'meta_description' => 'Original SEO description.',
        'status' => 'active',
    ]);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => $listing->title,
        'description' => $listing->description,
        'condition' => 'new',
        'meta_title' => '',
        'meta_description' => '',
    ];

    $response = $this->put(route('business.listings.update', $listing), $data);

    $response->assertRedirect(route('business.listings.index'));

    $listing->refresh();
    expect($listing->meta_title)->toBeNull();
    expect($listing->meta_description)->toBeNull();
});
