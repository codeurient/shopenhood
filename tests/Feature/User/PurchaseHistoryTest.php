<?php

use App\Models\Listing;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\User;
use App\Notifications\OrderCancelledByBuyerNotification;
use Illuminate\Support\Facades\Notification;

// ==========================================
// MY ORDERS (BUYER PURCHASE HISTORY)
// ==========================================

it('guest cannot view my-orders', function () {
    $this->get(route('user.purchases.index'))->assertRedirect(route('login'));
});

it('buyer can see their own purchases list', function () {
    $buyer = User::factory()->create();
    Purchase::factory()->forBuyer($buyer->id)->count(3)->create();

    $this->actingAs($buyer)
        ->get(route('user.purchases.index'))
        ->assertOk()
        ->assertViewIs('user.orders.index')
        ->assertViewHas('purchases');
});

it('buyer only sees their own purchases, not others', function () {
    $buyer = User::factory()->create();
    $otherBuyer = User::factory()->create();

    Purchase::factory()->forBuyer($buyer->id)->create(['purchase_number' => 'PUR-OWN-XXXXXX']);
    Purchase::factory()->forBuyer($otherBuyer->id)->create(['purchase_number' => 'PUR-OTHER-XXXXX']);

    $response = $this->actingAs($buyer)
        ->get(route('user.purchases.index'))
        ->assertOk();

    $purchases = $response->viewData('purchases');
    expect($purchases->pluck('buyer_id')->unique()->toArray())->toBe([$buyer->id]);
});

it('buyer can view their purchase detail', function () {
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();

    $this->actingAs($buyer)
        ->get(route('user.purchases.show', $purchase))
        ->assertOk()
        ->assertViewIs('user.orders.show')
        ->assertViewHas('purchase');
});

it('buyer cannot view another users purchase', function () {
    $buyer = User::factory()->create();
    $otherBuyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($otherBuyer->id)->create();

    $this->actingAs($buyer)
        ->get(route('user.purchases.show', $purchase))
        ->assertForbidden();
});

// ==========================================
// ORDER CANCELLATION (BUYER)
// ==========================================

it('guest cannot cancel an order', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    $this->post(route('user.orders.cancel', $order))
        ->assertRedirect(route('login'));
});

it('buyer can cancel a pending order', function () {
    Notification::fake();

    $buyer = User::factory()->create();
    $seller = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($buyer)
        ->post(route('user.orders.cancel', $order), ['reason' => 'Changed my mind'])
        ->assertRedirect();

    $order->refresh();
    expect($order->status)->toBe('cancelled');
    expect($order->cancellation_reason)->toBe('Changed my mind');
    Notification::assertSentTo($seller, OrderCancelledByBuyerNotification::class);
});

it('buyer can cancel without providing a reason', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($buyer)
        ->post(route('user.orders.cancel', $order))
        ->assertRedirect();

    expect($order->fresh()->status)->toBe('cancelled');
});

it('buyer cannot cancel another buyers order', function () {
    $buyer = User::factory()->create();
    $otherBuyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($otherBuyer->id)->create();
    $listing = Listing::factory()->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'buyer_id' => $otherBuyer->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($buyer)
        ->post(route('user.orders.cancel', $order))
        ->assertForbidden();
});

it('buyer cannot cancel a completed order', function () {
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();
    $listing = Listing::factory()->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'completed',
    ]);

    $this->actingAs($buyer)
        ->post(route('user.orders.cancel', $order))
        ->assertStatus(422);
});

// ==========================================
// PURCHASE STATUS SYNC
// ==========================================

it('purchase status becomes cancelled when all orders are cancelled by buyer', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create(['status' => 'pending']);
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($buyer)
        ->post(route('user.orders.cancel', $order), ['reason' => 'No longer needed'])
        ->assertRedirect();

    expect($purchase->fresh()->status)->toBe('cancelled');
});

it('purchase status becomes processing when order is shipped', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create(['status' => 'pending']);
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

    expect($purchase->fresh()->status)->toBe('processing');
});

it('purchase status becomes completed when all orders are delivered', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create(['status' => 'pending']);
    $listing = Listing::factory()->for($seller)->create();

    $order = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'shipped',
    ]);

    $this->actingAs($seller)
        ->post(route('user.sales.deliver', $order))
        ->assertRedirect();

    // Delivered is the terminal fulfillment state; purchase should be completed
    expect($purchase->fresh()->status)->toBe('completed');
});

it('purchase stays pending when one of two orders is cancelled', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create(['status' => 'pending']);
    $listing = Listing::factory()->for($seller)->create();

    $order1 = Order::factory()->create([
        'purchase_id' => $purchase->id,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);
    Order::factory()->create([
        'purchase_id' => $purchase->id,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'status' => 'pending',
    ]);

    $this->actingAs($buyer)
        ->post(route('user.orders.cancel', $order1), ['reason' => 'Changed mind'])
        ->assertRedirect();

    // One order still pending — purchase should remain pending
    expect($purchase->fresh()->status)->toBe('pending');
});
