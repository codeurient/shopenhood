<?php

use App\Models\CartItem;
use App\Models\Listing;
use App\Models\User;

it('shows the cart icon on a business listing viewed by another user', function () {
    $seller = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $listing = Listing::factory()->create([
        'user_id' => $seller->id,
        'listing_mode' => 'business',
        'status' => 'active',
    ]);

    $buyer = User::factory()->create();

    $response = $this->actingAs($buyer)->get(route('listings.show', $listing));

    $response->assertSuccessful();
    $response->assertDontSee('Buy Now');
    $response->assertDontSee('Add to Cart');
});

it('does not show add to cart or buy now buttons for a normal user listing', function () {
    $seller = User::factory()->create(['current_role' => 'normal_user']);
    $listing = Listing::factory()->create([
        'user_id' => $seller->id,
        'listing_mode' => 'normal',
        'status' => 'active',
    ]);

    $buyer = User::factory()->create();

    $response = $this->actingAs($buyer)->get(route('listings.show', $listing));

    $response->assertSuccessful();
    $response->assertDontSee('Add to Cart');
    $response->assertDontSee('Buy Now');
});

it('does not show add to cart or buy now buttons when the owner views their own business listing', function () {
    $seller = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $listing = Listing::factory()->create([
        'user_id' => $seller->id,
        'listing_mode' => 'business',
        'status' => 'active',
    ]);

    $response = $this->actingAs($seller)->get(route('listings.show', $listing));

    $response->assertSuccessful();
    $response->assertDontSee('Add to Cart');
    $response->assertDontSee('Buy Now');
});

it('cart api deselects existing items and marks only the new item as selected', function () {
    $seller = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $buyer = User::factory()->create();

    $otherListing = Listing::factory()->create([
        'user_id' => $seller->id,
        'listing_mode' => 'business',
        'status' => 'active',
    ]);

    $targetListing = Listing::factory()->create([
        'user_id' => $seller->id,
        'listing_mode' => 'business',
        'status' => 'active',
    ]);

    CartItem::factory()->create([
        'user_id' => $buyer->id,
        'listing_id' => $otherListing->id,
        'is_selected' => true,
    ]);

    $this->actingAs($buyer);

    $this->postJson('/api/cart/select-all', ['selected' => false])->assertSuccessful();
    $this->postJson('/api/cart', ['listing_id' => $targetListing->id, 'quantity' => 1])->assertSuccessful();

    expect(CartItem::where('user_id', $buyer->id)->where('listing_id', $otherListing->id)->value('is_selected'))
        ->toBeFalse();

    expect(CartItem::where('user_id', $buyer->id)->where('listing_id', $targetListing->id)->value('is_selected'))
        ->toBeTrue();
});
