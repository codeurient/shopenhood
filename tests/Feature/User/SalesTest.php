<?php

use App\Models\Listing;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\User;
use App\Notifications\OrderCannotShipNotification;
use App\Notifications\OrderShippedNotification;
use Illuminate\Support\Facades\Notification;

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

// ==========================================
// SELLER ACTIONS: SHIP
// ==========================================

it('guest cannot ship an order', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    $this->post(route('user.sales.ship', $order))
        ->assertRedirect(route('login'));
});

it('seller can mark an order as shipped', function () {
    Notification::fake();

    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($seller)
        ->post(route('user.sales.ship', $order))
        ->assertRedirect();

    $order->refresh();
    expect($order->status)->toBe('shipped');
    expect($order->shipped_at)->not->toBeNull();
    Notification::assertSentTo($buyer, OrderShippedNotification::class);
});

it('seller can include a tracking number when shipping', function () {
    Notification::fake();

    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($seller)
        ->post(route('user.sales.ship', $order), ['tracking_number' => 'TRK-123456'])
        ->assertRedirect();

    expect($order->fresh()->tracking_number)->toBe('TRK-123456');
});

it('seller cannot ship another sellers order', function () {
    $sellerA = User::factory()->create();
    $sellerB = User::factory()->create();
    $purchase = Purchase::factory()->create();
    $listing = Listing::factory()->for($sellerB)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $sellerB->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($sellerA)
        ->post(route('user.sales.ship', $order))
        ->assertForbidden();
});

it('seller cannot ship an already-delivered order', function () {
    $seller = User::factory()->create();
    $purchase = Purchase::factory()->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'delivered',
    ]);

    $this->actingAs($seller)
        ->post(route('user.sales.ship', $order))
        ->assertStatus(422);
});

// ==========================================
// SELLER ACTIONS: DELIVER
// ==========================================

it('seller can mark a shipped order as delivered', function () {
    $seller = User::factory()->create();
    $purchase = Purchase::factory()->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'shipped',
    ]);

    $this->actingAs($seller)
        ->post(route('user.sales.deliver', $order))
        ->assertRedirect();

    $order->refresh();
    expect($order->status)->toBe('delivered');
    expect($order->delivered_at)->not->toBeNull();
});

it('seller cannot mark a pending order as delivered', function () {
    $seller = User::factory()->create();
    $purchase = Purchase::factory()->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($seller)
        ->post(route('user.sales.deliver', $order))
        ->assertStatus(422);
});

// ==========================================
// SELLER ACTIONS: CANNOT SHIP
// ==========================================

it('seller can cancel an order with a reason', function () {
    Notification::fake();

    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($seller)
        ->post(route('user.sales.cannot-ship', $order), ['reason' => 'Item is out of stock'])
        ->assertRedirect();

    $order->refresh();
    expect($order->status)->toBe('cancelled');
    expect($order->cancellation_reason)->toBe('Item is out of stock');
    Notification::assertSentTo($buyer, OrderCannotShipNotification::class);
});

it('cannot ship requires a reason', function () {
    $seller = User::factory()->create();
    $purchase = Purchase::factory()->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($seller)
        ->from(route('user.sales.index'))
        ->post(route('user.sales.cannot-ship', $order), ['reason' => ''])
        ->assertSessionHasErrors('reason');
});

it('seller cannot cancel another sellers order via cannot-ship', function () {
    $sellerA = User::factory()->create();
    $sellerB = User::factory()->create();
    $purchase = Purchase::factory()->create();
    $listing = Listing::factory()->for($sellerB)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $sellerB->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($sellerA)
        ->post(route('user.sales.cannot-ship', $order), ['reason' => 'Test'])
        ->assertForbidden();
});
