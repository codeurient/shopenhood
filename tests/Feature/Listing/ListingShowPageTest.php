<?php

use App\Models\Listing;
use App\Models\Order;
use App\Models\User;

// ==========================================
// SHOW PAGE LOADS
// ==========================================

test('listing show page loads for guest', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);

    $this->get(route('listings.show', $listing->slug))
        ->assertSuccessful();
});

test('listing show page loads for authenticated user', function () {
    $user = User::factory()->create();
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);

    $this->actingAs($user)
        ->get(route('listings.show', $listing->slug))
        ->assertSuccessful();
});

// ==========================================
// FAVORITES STATE
// ==========================================

test('show page passes isFavorited as false for guest', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertViewHas('isFavorited', false);
});

test('show page passes isFavorited as false when user has not favorited the listing', function () {
    $user = User::factory()->create();
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);

    $response = $this->actingAs($user)
        ->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertViewHas('isFavorited', false);
});

test('show page passes isFavorited as true when user has favorited the listing', function () {
    $user = User::factory()->create();
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);
    $user->favoriteListings()->attach($listing->id);

    $response = $this->actingAs($user)
        ->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertViewHas('isFavorited', true);
});

// ==========================================
// LISTING SOLD COUNT
// ==========================================

test('show page passes listingTotalSold as zero when no orders exist', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertViewHas('listingTotalSold', 0);
});

test('show page passes correct listingTotalSold from non-cancelled orders', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'user_id' => $seller->id,
    ]);

    Order::factory()->create(['listing_id' => $listing->id, 'seller_id' => $seller->id, 'quantity' => 3, 'status' => 'completed']);
    Order::factory()->create(['listing_id' => $listing->id, 'seller_id' => $seller->id, 'quantity' => 2, 'status' => 'pending']);
    // This one should NOT count
    Order::factory()->create(['listing_id' => $listing->id, 'seller_id' => $seller->id, 'quantity' => 10, 'status' => 'cancelled']);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertViewHas('listingTotalSold', 5);
});

// ==========================================
// SELLER TOTAL SOLD
// ==========================================

test('show page passes sellerTotalSold counting all seller listings', function () {
    $seller = User::factory()->create();
    $listing1 = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'user_id' => $seller->id]);
    $listing2 = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'user_id' => $seller->id]);

    Order::factory()->create(['listing_id' => $listing1->id, 'seller_id' => $seller->id, 'quantity' => 4, 'status' => 'completed']);
    Order::factory()->create(['listing_id' => $listing2->id, 'seller_id' => $seller->id, 'quantity' => 6, 'status' => 'delivered']);
    // Cancelled orders excluded
    Order::factory()->create(['listing_id' => $listing1->id, 'seller_id' => $seller->id, 'quantity' => 99, 'status' => 'cancelled']);

    $response = $this->get(route('listings.show', $listing1->slug));

    $response->assertSuccessful();
    $response->assertViewHas('sellerTotalSold', 10);
});

// ==========================================
// SELLER AVG RATING
// ==========================================

test('show page passes sellerAvgRating as zero when no reviews', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertViewHas('sellerAvgRating', 0.0);
});
