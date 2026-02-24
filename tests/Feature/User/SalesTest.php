<?php

use App\Models\Listing;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\User;

// ==========================================
// MY SALES (SELLER INCOMING ORDERS)
// ==========================================

it('guest cannot view my-sales', function () {
    $this->get(route('user.sales.index'))->assertRedirect(route('login'));
});

it('seller sees their incoming orders', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listing = Listing::factory()->for($seller)->create(['status' => 'active', 'listing_mode' => 'normal']);

    Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($seller)
        ->get(route('user.sales.index'))
        ->assertOk()
        ->assertViewIs('user.sales.index')
        ->assertViewHas('orders');
});

it('seller does not see other sellers orders', function () {
    $sellerA = User::factory()->create();
    $sellerB = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listingB = Listing::factory()->for($sellerB)->create(['status' => 'active', 'listing_mode' => 'normal']);

    Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $sellerB->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listingB->id,
    ]);

    $response = $this->actingAs($sellerA)
        ->get(route('user.sales.index'))
        ->assertOk();

    $orders = $response->viewData('orders');
    expect($orders)->toHaveCount(0);
});

it('seller can filter orders by status', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listing = Listing::factory()->for($seller)->create(['status' => 'active', 'listing_mode' => 'normal']);

    Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);
    Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($seller)
        ->get(route('user.sales.index', ['status' => 'pending']))
        ->assertOk();

    $orders = $response->viewData('orders');
    expect($orders)->toHaveCount(1);
    expect($orders->first()->status)->toBe('pending');
});
