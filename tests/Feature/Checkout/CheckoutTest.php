<?php

use App\Models\CartItem;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserAddress;

// ==========================================
// HELPERS
// ==========================================

function makeSellerWithListing(array $listingAttrs = []): array
{
    $seller = User::factory()->create();
    $listing = Listing::factory()->for($seller)->create(array_merge([
        'status' => 'active',
        'base_price' => 50.00,
        'currency' => 'USD',
        'has_domestic_delivery' => false,
        'listing_mode' => 'normal',
    ], $listingAttrs));

    return [$seller, $listing];
}

function addToCart(User $buyer, Listing $listing, int $qty = 1, bool $selected = true): CartItem
{
    return CartItem::factory()->create([
        'user_id' => $buyer->id,
        'listing_id' => $listing->id,
        'variation_id' => null,
        'quantity' => $qty,
        'is_selected' => $selected,
    ]);
}

function makeAddress(User $user, bool $isDefault = true): UserAddress
{
    return UserAddress::factory()->create([
        'user_id' => $user->id,
        'is_default' => $isDefault,
        'recipient_name' => 'Test Buyer',
        'phone' => '+1234567890',
        'country' => 'AZ',
        'city' => 'Baku',
        'street' => '123 Test St',
    ]);
}

// ==========================================
// PREPARE ENDPOINT
// ==========================================

it('prepare returns 401 for guests', function () {
    $this->getJson(route('checkout.prepare'))->assertUnauthorized();
});

it('prepare returns 422 when no items are selected', function () {
    $buyer = User::factory()->create();
    [$seller, $listing] = makeSellerWithListing();
    addToCart($buyer, $listing, selected: false);

    $this->actingAs($buyer)
        ->getJson(route('checkout.prepare'))
        ->assertUnprocessable()
        ->assertJson(['message' => 'No items selected for checkout.']);
});

it('prepare groups selected items by seller', function () {
    $buyer = User::factory()->create();
    [$sellerA, $listingA] = makeSellerWithListing();
    [$sellerB, $listingB] = makeSellerWithListing();

    addToCart($buyer, $listingA, qty: 2);
    addToCart($buyer, $listingB, qty: 1);

    $response = $this->actingAs($buyer)
        ->getJson(route('checkout.prepare'))
        ->assertOk();

    $sellers = $response->json('sellers');
    expect($sellers)->toHaveCount(2);

    $sellerIds = collect($sellers)->pluck('seller_id')->toArray();
    expect($sellerIds)->toContain($sellerA->id);
    expect($sellerIds)->toContain($sellerB->id);
});

it('prepare returns empty delivery options when listing has no delivery configured', function () {
    $buyer = User::factory()->create();
    [$seller, $listing] = makeSellerWithListing(['has_domestic_delivery' => false]);
    addToCart($buyer, $listing);

    $response = $this->actingAs($buyer)
        ->getJson(route('checkout.prepare'))
        ->assertOk();

    $deliveryOptions = $response->json('sellers.0.delivery_options');
    expect($deliveryOptions)->toBeArray()->toBeEmpty();
});

it('prepare includes free shipping option when domestic_delivery_price is 0', function () {
    $buyer = User::factory()->create();
    [$seller, $listing] = makeSellerWithListing([
        'has_domestic_delivery' => true,
        'domestic_delivery_price' => 0,
    ]);
    addToCart($buyer, $listing);

    $response = $this->actingAs($buyer)
        ->getJson(route('checkout.prepare'))
        ->assertOk();

    $keys = collect($response->json('sellers.0.delivery_options'))->pluck('key')->toArray();
    expect($keys)->toContain('free_shipping');
});

it('prepare includes standard delivery with cost when domestic_delivery_price is positive', function () {
    $buyer = User::factory()->create();
    [$seller, $listing] = makeSellerWithListing([
        'has_domestic_delivery' => true,
        'domestic_delivery_price' => 8.50,
    ]);
    addToCart($buyer, $listing);

    $response = $this->actingAs($buyer)
        ->getJson(route('checkout.prepare'))
        ->assertOk();

    $options = collect($response->json('sellers.0.delivery_options'));
    $standard = $options->firstWhere('key', 'standard_delivery');
    expect($standard)->not->toBeNull();
    expect((float) $standard['cost'])->toBe(8.50);
    expect($standard['paid_by'])->toBe('buyer');
});

it('prepare returns user addresses', function () {
    $buyer = User::factory()->create();
    makeAddress($buyer);
    [$seller, $listing] = makeSellerWithListing();
    addToCart($buyer, $listing);

    $response = $this->actingAs($buyer)
        ->getJson(route('checkout.prepare'))
        ->assertOk();

    expect($response->json('addresses'))->toHaveCount(1);
    expect($response->json('default_address_id'))->not->toBeNull();
});

// ==========================================
// CONFIRM ENDPOINT
// ==========================================

it('confirm returns 401 for guests', function () {
    $this->postJson(route('checkout.confirm'), [])->assertUnauthorized();
});

it('confirm requires address_id and payment_method', function () {
    $buyer = User::factory()->create();

    $this->actingAs($buyer)
        ->postJson(route('checkout.confirm'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['address_id', 'payment_method']);
});

it('confirm creates a purchase record', function () {
    $buyer = User::factory()->create();
    $address = makeAddress($buyer);
    [$seller, $listing] = makeSellerWithListing();
    addToCart($buyer, $listing);

    $this->actingAs($buyer)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
        'delivery_selections' => [$seller->id => 'pickup'],
    ])->assertOk()->assertJsonPath('success', true);

    expect(Purchase::where('buyer_id', $buyer->id)->count())->toBe(1);
});

it('confirm creates one order per selected cart item', function () {
    $buyer = User::factory()->create();
    $address = makeAddress($buyer);
    [$sellerA, $listingA] = makeSellerWithListing();
    [$sellerB, $listingB] = makeSellerWithListing();

    addToCart($buyer, $listingA);
    addToCart($buyer, $listingB);

    $this->actingAs($buyer)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
        'delivery_selections' => [
            $sellerA->id => 'pickup',
            $sellerB->id => 'pickup',
        ],
    ])->assertOk();

    expect(Order::where('buyer_id', $buyer->id)->count())->toBe(2);
    expect(Order::where('seller_id', $sellerA->id)->count())->toBe(1);
    expect(Order::where('seller_id', $sellerB->id)->count())->toBe(1);
});

it('confirm removes selected cart items but keeps unselected ones', function () {
    $buyer = User::factory()->create();
    $address = makeAddress($buyer);
    [$seller, $listing] = makeSellerWithListing();
    [$seller2, $listing2] = makeSellerWithListing();

    addToCart($buyer, $listing, selected: true);
    addToCart($buyer, $listing2, selected: false);  // unselected — should stay

    $this->actingAs($buyer)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
        'delivery_selections' => [$seller->id => 'pickup'],
    ])->assertOk();

    expect(CartItem::where('user_id', $buyer->id)->count())->toBe(1);
    expect(CartItem::where('user_id', $buyer->id)->where('is_selected', false)->count())->toBe(1);
});

it('confirm rejects an address not owned by the authenticated user', function () {
    $buyer = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherAddress = makeAddress($otherUser);
    [$seller, $listing] = makeSellerWithListing();
    addToCart($buyer, $listing);

    $this->actingAs($buyer)->postJson(route('checkout.confirm'), [
        'address_id' => $otherAddress->id,
        'payment_method' => 'cash_on_delivery',
        'delivery_selections' => [$seller->id => 'pickup'],
    ])->assertNotFound();
});

it('confirm returns 422 when cart has no selected items', function () {
    $buyer = User::factory()->create();
    $address = makeAddress($buyer);
    [$seller, $listing] = makeSellerWithListing();
    addToCart($buyer, $listing, selected: false);

    $this->actingAs($buyer)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
        'delivery_selections' => [$seller->id => 'pickup'],
    ])->assertUnprocessable();
});

it('confirm stores delivery_option_name and delivery_cost_paid_by on each order', function () {
    $buyer = User::factory()->create();
    $address = makeAddress($buyer);
    [$seller, $listing] = makeSellerWithListing([
        'has_domestic_delivery' => true,
        'domestic_delivery_price' => 5.00,
    ]);
    addToCart($buyer, $listing);

    $this->actingAs($buyer)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
        'delivery_selections' => [$seller->id => 'standard_delivery'],
    ])->assertOk();

    $order = Order::where('buyer_id', $buyer->id)->first();
    expect($order->delivery_option_name)->toBe('Standard Delivery');
    expect($order->delivery_cost_paid_by)->toBe('buyer');
    expect((float) $order->shipping_cost)->toBe(5.00);
});

it('confirm stores address snapshot in purchase', function () {
    $buyer = User::factory()->create();
    $address = makeAddress($buyer);
    [$seller, $listing] = makeSellerWithListing();
    addToCart($buyer, $listing);

    $this->actingAs($buyer)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
        'delivery_selections' => [$seller->id => 'pickup'],
    ])->assertOk();

    $purchase = Purchase::where('buyer_id', $buyer->id)->first();
    expect($purchase->address_snapshot)->not->toBeNull();
    expect($purchase->address_snapshot['recipient_name'])->toBe('Test Buyer');
});

it('confirm returns redirect url to purchase show page', function () {
    $buyer = User::factory()->create();
    $address = makeAddress($buyer);
    [$seller, $listing] = makeSellerWithListing();
    addToCart($buyer, $listing);

    $response = $this->actingAs($buyer)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
        'delivery_selections' => [$seller->id => 'pickup'],
    ])->assertOk();

    expect($response->json('redirect_url'))->toContain('/my-orders/');
});

// ==========================================
// OWNERSHIP RESTRICTION — CONFIRM
// ==========================================

it('confirm blocks checkout when selected cart items include the buyer\'s own listing', function () {
    $owner = User::factory()->create();
    $address = makeAddress($owner);

    // Owner adds their own listing to cart directly (bypassing cart store validation)
    $listing = Listing::factory()->for($owner)->create([
        'status' => 'active',
        'base_price' => 30.00,
        'currency' => 'USD',
        'listing_mode' => 'business',
    ]);
    addToCart($owner, $listing, selected: true);

    $this->actingAs($owner)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
    ])->assertUnprocessable()
        ->assertJson(['message' => 'You cannot purchase your own item.']);
});

it('confirm blocks checkout when only one of multiple items belongs to the buyer', function () {
    $owner = User::factory()->create();
    $address = makeAddress($owner);

    // Own listing
    $ownListing = Listing::factory()->for($owner)->create([
        'status' => 'active',
        'base_price' => 20.00,
        'currency' => 'USD',
        'listing_mode' => 'business',
    ]);
    addToCart($owner, $ownListing, selected: true);

    // Listing from another seller
    [$otherSeller, $otherListing] = makeSellerWithListing(['listing_mode' => 'business']);
    addToCart($owner, $otherListing, selected: true);

    $this->actingAs($owner)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
    ])->assertUnprocessable()
        ->assertJson(['message' => 'You cannot purchase your own item.']);
});

it('confirm does not create any order when blocked by ownership restriction', function () {
    $owner = User::factory()->create();
    $address = makeAddress($owner);

    $listing = Listing::factory()->for($owner)->create([
        'status' => 'active',
        'base_price' => 15.00,
        'currency' => 'USD',
        'listing_mode' => 'business',
    ]);
    addToCart($owner, $listing, selected: true);

    $this->actingAs($owner)->postJson(route('checkout.confirm'), [
        'address_id' => $address->id,
        'payment_method' => 'cash_on_delivery',
    ])->assertUnprocessable();

    expect(Purchase::where('buyer_id', $owner->id)->count())->toBe(0);
    expect(Order::where('buyer_id', $owner->id)->count())->toBe(0);
});
