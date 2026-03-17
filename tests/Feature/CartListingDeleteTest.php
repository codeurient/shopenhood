<?php

use App\Models\CartItem;
use App\Models\Listing;
use App\Models\User;

it('removes cart items when a listing is soft deleted', function () {
    $seller = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $buyer = User::factory()->create();

    $listing = Listing::factory()->create([
        'user_id' => $seller->id,
        'listing_mode' => 'business',
        'status' => 'active',
    ]);

    CartItem::factory()->create([
        'user_id' => $buyer->id,
        'listing_id' => $listing->id,
    ]);

    expect(CartItem::where('listing_id', $listing->id)->count())->toBe(1);

    $listing->delete();

    expect(CartItem::where('listing_id', $listing->id)->count())->toBe(0);
});

it('removes cart items for all users when a listing is deleted', function () {
    $seller = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $buyer1 = User::factory()->create();
    $buyer2 = User::factory()->create();

    $listing = Listing::factory()->create([
        'user_id' => $seller->id,
        'listing_mode' => 'business',
        'status' => 'active',
    ]);

    CartItem::factory()->create(['user_id' => $buyer1->id, 'listing_id' => $listing->id]);
    CartItem::factory()->create(['user_id' => $buyer2->id, 'listing_id' => $listing->id]);

    expect(CartItem::where('listing_id', $listing->id)->count())->toBe(2);

    $listing->delete();

    expect(CartItem::where('listing_id', $listing->id)->count())->toBe(0);
});

it('does not remove cart items for other listings when one listing is deleted', function () {
    $seller = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $buyer = User::factory()->create();

    $listing1 = Listing::factory()->create(['user_id' => $seller->id, 'listing_mode' => 'business', 'status' => 'active']);
    $listing2 = Listing::factory()->create(['user_id' => $seller->id, 'listing_mode' => 'business', 'status' => 'active']);

    CartItem::factory()->create(['user_id' => $buyer->id, 'listing_id' => $listing1->id]);
    CartItem::factory()->create(['user_id' => $buyer->id, 'listing_id' => $listing2->id]);

    $listing1->delete();

    expect(CartItem::where('listing_id', $listing1->id)->count())->toBe(0);
    expect(CartItem::where('listing_id', $listing2->id)->count())->toBe(1);
});

it('removes cart items when a listing is force deleted', function () {
    $seller = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $buyer = User::factory()->create();

    $listing = Listing::factory()->create([
        'user_id' => $seller->id,
        'listing_mode' => 'business',
        'status' => 'active',
    ]);

    CartItem::factory()->create(['user_id' => $buyer->id, 'listing_id' => $listing->id]);

    expect(CartItem::where('listing_id', $listing->id)->count())->toBe(1);

    // Soft delete first, then force delete (typical flow)
    $listing->delete();
    $listing->forceDelete();

    expect(CartItem::where('listing_id', $listing->id)->count())->toBe(0);
});
