<?php

use App\Models\CartItem;
use App\Models\Listing;
use App\Models\User;

// ==========================================
// OWNERSHIP RESTRICTION — CART STORE
// ==========================================

it('owner cannot add their own listing to the cart', function () {
    $owner = User::factory()->create();
    $listing = Listing::factory()->for($owner)->create([
        'status' => 'active',
        'listing_mode' => 'business',
    ]);

    $this->actingAs($owner)
        ->postJson('/api/cart', [
            'listing_id' => $listing->id,
            'quantity' => 1,
        ])
        ->assertUnprocessable()
        ->assertJson(['message' => 'You cannot add your own listing to the cart.']);
});

it('owner adding their own listing does not create a cart item', function () {
    $owner = User::factory()->create();
    $listing = Listing::factory()->for($owner)->create([
        'status' => 'active',
        'listing_mode' => 'business',
    ]);

    $this->actingAs($owner)->postJson('/api/cart', [
        'listing_id' => $listing->id,
        'quantity' => 1,
    ]);

    expect(CartItem::where('user_id', $owner->id)->count())->toBe(0);
});

it('buyer can add another seller\'s listing to the cart', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create();
    $listing = Listing::factory()->for($seller)->create([
        'status' => 'active',
        'listing_mode' => 'business',
    ]);

    $this->actingAs($buyer)
        ->postJson('/api/cart', [
            'listing_id' => $listing->id,
            'quantity' => 1,
        ])
        ->assertOk()
        ->assertJson(['message' => 'Added to cart']);

    expect(CartItem::where('user_id', $buyer->id)->count())->toBe(1);
});

it('guest cannot add to cart', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->for($seller)->create([
        'status' => 'active',
        'listing_mode' => 'business',
    ]);

    $this->postJson('/api/cart', [
        'listing_id' => $listing->id,
        'quantity' => 1,
    ])->assertUnauthorized();
});
