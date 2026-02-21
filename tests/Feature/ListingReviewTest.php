<?php

use App\Models\Listing;
use App\Models\ListingReview;
use App\Models\Order;
use App\Models\User;

function makeDeliveredOrder(User $buyer, Listing $listing, string $status = 'delivered'): Order
{
    $seller = User::factory()->create();

    return Order::create([
        'order_number' => 'ORD-'.uniqid(),
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'listing_id' => $listing->id,
        'quantity' => 1,
        'unit_price' => $listing->base_price ?? 10,
        'subtotal' => $listing->base_price ?? 10,
        'total_amount' => $listing->base_price ?? 10,
        'status' => $status,
        'payment_status' => 'paid',
    ]);
}

// ──────────────────────────────────────────────────────────────────────────────
// Listing show page
// ──────────────────────────────────────────────────────────────────────────────

it('shows listing detail page with no reviews', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertSee('No reviews yet');
});

it('does not show reviews section for normal listings', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'normal']);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertDontSee('No reviews yet');
    $response->assertDontSee('Write a Review');
});

it('shows quantity selector on business listing page', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertSee('id="qty-selector"', false);
});

it('does not show quantity selector on normal listing page', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'normal']);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertDontSee('id="qty-selector"', false);
});

it('displays existing reviews on listing page', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    $order = makeDeliveredOrder($buyer, $listing);

    ListingReview::create([
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
        'order_id' => $order->id,
        'rating' => 5,
        'title' => 'Great product',
        'body' => 'Really loved it!',
        'is_verified_purchase' => true,
    ]);

    $response = $this->get(route('listings.show', $listing->slug));

    $response->assertSuccessful();
    $response->assertSee('Great product');
    $response->assertSee('Really loved it!');
    $response->assertSee('Verified');
});

// ──────────────────────────────────────────────────────────────────────────────
// Submit review
// ──────────────────────────────────────────────────────────────────────────────

it('allows a buyer with a delivered order to submit a review', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    makeDeliveredOrder($buyer, $listing, 'delivered');

    $this->actingAs($buyer);

    $response = $this->post(route('listings.reviews.store', $listing), [
        'rating' => 5,
        'title' => 'Excellent',
        'body' => 'Really happy with the purchase.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('listing_reviews', [
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
        'rating' => 5,
        'title' => 'Excellent',
    ]);
});

it('allows a buyer with a completed order to submit a review', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    makeDeliveredOrder($buyer, $listing, 'completed');

    $this->actingAs($buyer);

    $response = $this->post(route('listings.reviews.store', $listing), [
        'rating' => 4,
        'body' => 'Good product.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('listing_reviews', [
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
        'rating' => 4,
    ]);
});

it('prevents reviewing a normal listing', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'normal']);
    $buyer = User::factory()->create();
    makeDeliveredOrder($buyer, $listing);

    $this->actingAs($buyer);

    $response = $this->post(route('listings.reviews.store', $listing), [
        'rating' => 5,
        'body' => 'Nice product.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('listing_reviews', [
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
    ]);
});

it('prevents a user without a delivered order from reviewing', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    // No order created

    $this->actingAs($buyer);

    $response = $this->post(route('listings.reviews.store', $listing), [
        'rating' => 5,
        'body' => 'Nice!',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('listing_reviews', [
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
    ]);
});

it('prevents reviewing when order status is only shipped (not delivered)', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    makeDeliveredOrder($buyer, $listing, 'shipped');

    $this->actingAs($buyer);

    $response = $this->post(route('listings.reviews.store', $listing), [
        'rating' => 5,
        'body' => 'Nice!',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('prevents duplicate reviews from the same buyer', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    $order = makeDeliveredOrder($buyer, $listing);

    ListingReview::create([
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
        'order_id' => $order->id,
        'rating' => 4,
        'is_verified_purchase' => true,
    ]);

    $this->actingAs($buyer);

    $response = $this->post(route('listings.reviews.store', $listing), [
        'rating' => 5,
        'body' => 'Trying again.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseCount('listing_reviews', 1);
});

it('requires a rating between 1 and 5', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    makeDeliveredOrder($buyer, $listing);

    $this->actingAs($buyer);

    $response = $this->post(route('listings.reviews.store', $listing), [
        'rating' => 6,
    ]);

    $response->assertSessionHasErrors('rating');
});

it('redirects guests to login when trying to review', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);

    $response = $this->post(route('listings.reviews.store', $listing), [
        'rating' => 5,
    ]);

    $response->assertRedirect(route('login'));
});

// ──────────────────────────────────────────────────────────────────────────────
// Delete review
// ──────────────────────────────────────────────────────────────────────────────

it('allows a user to delete their own review', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    $order = makeDeliveredOrder($buyer, $listing);

    $review = ListingReview::create([
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
        'order_id' => $order->id,
        'rating' => 3,
        'is_verified_purchase' => true,
    ]);

    $this->actingAs($buyer);

    $response = $this->delete(route('listings.reviews.destroy', $review));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertSoftDeleted('listing_reviews', ['id' => $review->id]);
});

it('prevents a user from deleting another user\'s review', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true, 'listing_mode' => 'business']);
    $buyer = User::factory()->create();
    $otherUser = User::factory()->create();
    $order = makeDeliveredOrder($buyer, $listing);

    $review = ListingReview::create([
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
        'order_id' => $order->id,
        'rating' => 3,
        'is_verified_purchase' => true,
    ]);

    $this->actingAs($otherUser);

    $response = $this->delete(route('listings.reviews.destroy', $review));

    $response->assertForbidden();
    $this->assertDatabaseHas('listing_reviews', ['id' => $review->id, 'deleted_at' => null]);
});
