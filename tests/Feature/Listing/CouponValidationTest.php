<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponRestriction;
use App\Models\Listing;
use App\Models\User;

// ==========================================
// HELPERS
// ==========================================

function makeListing(array $attributes = []): Listing
{
    return Listing::factory()->normalMode()->create(array_merge([
        'base_price' => 100.00,
        'status' => 'active',
    ], $attributes));
}

function activeCoupon(array $attributes = []): Coupon
{
    return Coupon::factory()->create(array_merge([
        'applicable_to' => 'all',
        'is_active' => true,
        'usage_limit' => null,
        'usage_count' => 0,
        'starts_at' => null,
        'expires_at' => null,
        'min_purchase_amount' => null,
        'max_discount_amount' => null,
    ], $attributes));
}

function postCoupon(Listing $listing, array $payload = []): \Illuminate\Testing\TestResponse
{
    return test()->postJson(route('listings.coupon.validate', $listing), $payload);
}

// ==========================================
// VALIDATION
// ==========================================

it('requires a coupon code', function () {
    $listing = makeListing();

    postCoupon($listing, [])->assertUnprocessable();
});

// ==========================================
// INVALID COUPON STATES
// ==========================================

it('returns error for unknown coupon code', function () {
    $listing = makeListing();

    postCoupon($listing, ['code' => 'DOESNOTEXIST'])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'Invalid coupon code.']);
});

it('returns error for inactive coupon', function () {
    $listing = makeListing();
    $coupon = activeCoupon(['is_active' => false]);

    postCoupon($listing, ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'This coupon is no longer active.']);
});

it('returns error for expired coupon', function () {
    $listing = makeListing();
    $coupon = Coupon::factory()->expired()->create(['applicable_to' => 'all']);

    postCoupon($listing, ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'This coupon has expired.']);
});

it('returns error for coupon that has not started yet', function () {
    $listing = makeListing();
    $coupon = activeCoupon(['starts_at' => now()->addDays(2)]);

    postCoupon($listing, ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'This coupon is not yet valid.']);
});

// ==========================================
// USAGE LIMIT
// ==========================================

it('returns "The limit has been reached." when usage_limit is exhausted', function () {
    $listing = makeListing();
    $coupon = activeCoupon(['usage_limit' => 5, 'usage_count' => 5]);

    postCoupon($listing, ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'The limit has been reached.']);
});

it('returns "The limit has been reached." even when the product owner tries to apply the exhausted coupon', function () {
    $owner = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $listing = makeListing(['user_id' => $owner->id]);
    $coupon = activeCoupon(['user_id' => $owner->id, 'usage_limit' => 3, 'usage_count' => 3]);

    $this->actingAs($owner)
        ->postJson(route('listings.coupon.validate', $listing), ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'The limit has been reached.']);
});

it('allows apply when usage_count is below usage_limit', function () {
    $listing = makeListing();
    $coupon = activeCoupon(['usage_limit' => 10, 'usage_count' => 9, 'type' => 'fixed', 'value' => 10]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 100])
        ->assertOk()
        ->assertJson(['success' => true]);
});

it('allows apply when usage_limit is null (unlimited)', function () {
    $listing = makeListing();
    $coupon = activeCoupon(['usage_limit' => null, 'usage_count' => 999, 'type' => 'fixed', 'value' => 5]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 50])
        ->assertOk()
        ->assertJson(['success' => true]);
});

// ==========================================
// PER-USER LIMIT
// ==========================================

it('returns error when authenticated user exceeds per_user_limit', function () {
    $user = User::factory()->create();
    $listing = makeListing();
    $coupon = activeCoupon(['per_user_limit' => 1]);
    $coupon->usages()->create(['user_id' => $user->id, 'discount_amount' => 5]);

    $this->actingAs($user)
        ->postJson(route('listings.coupon.validate', $listing), ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false]);
});

// ==========================================
// RESTRICTIONS
// ==========================================

it('returns error when coupon is restricted to listings and this listing is not included', function () {
    $listing = makeListing();
    $otherListing = makeListing();
    $coupon = activeCoupon(['applicable_to' => 'listings']);
    CouponRestriction::create([
        'coupon_id' => $coupon->id,
        'restrictable_type' => Listing::class,
        'restrictable_id' => $otherListing->id,
    ]);

    postCoupon($listing, ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'This coupon is not applicable to this product.']);
});

it('returns success when coupon is restricted to listings and this listing is included', function () {
    $listing = makeListing();
    $coupon = activeCoupon(['applicable_to' => 'listings', 'type' => 'fixed', 'value' => 10]);
    CouponRestriction::create([
        'coupon_id' => $coupon->id,
        'restrictable_type' => Listing::class,
        'restrictable_id' => $listing->id,
    ]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 100])
        ->assertOk()
        ->assertJson(['success' => true]);
});

it('returns error when coupon is restricted to categories and this listing category is not included', function () {
    $category = Category::factory()->create();
    $otherCategory = Category::factory()->create();
    $listing = makeListing(['category_id' => $category->id]);
    $coupon = activeCoupon(['applicable_to' => 'categories']);
    CouponRestriction::create([
        'coupon_id' => $coupon->id,
        'restrictable_type' => Category::class,
        'restrictable_id' => $otherCategory->id,
    ]);

    postCoupon($listing, ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'This coupon is not applicable to this category.']);
});

it('returns error for user-type coupons', function () {
    $listing = makeListing();
    $coupon = activeCoupon(['applicable_to' => 'users']);

    postCoupon($listing, ['code' => $coupon->code])
        ->assertUnprocessable()
        ->assertJson(['success' => false, 'message' => 'This coupon is not applicable here.']);
});

// ==========================================
// MINIMUM PURCHASE AMOUNT
// ==========================================

it('returns error when price is below minimum purchase amount', function () {
    $listing = makeListing(['base_price' => 20.00]);
    $coupon = activeCoupon(['min_purchase_amount' => 50.00, 'type' => 'fixed', 'value' => 10]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 20])
        ->assertUnprocessable()
        ->assertJson(['success' => false]);
});

it('applies coupon when price meets minimum purchase amount', function () {
    $listing = makeListing(['base_price' => 100.00]);
    $coupon = activeCoupon(['min_purchase_amount' => 50.00, 'type' => 'fixed', 'value' => 10]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 100])
        ->assertOk()
        ->assertJson(['success' => true]);
});

// ==========================================
// DISCOUNT CALCULATION
// ==========================================

it('calculates fixed discount correctly', function () {
    $listing = makeListing(['base_price' => 100.00]);
    $coupon = activeCoupon(['type' => 'fixed', 'value' => 15, 'max_discount_amount' => null]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 100])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'coupon' => ['discount_amount' => 15.0, 'final_price' => 85.0],
        ]);
});

it('calculates percentage discount correctly', function () {
    $listing = makeListing(['base_price' => 200.00]);
    $coupon = activeCoupon(['type' => 'percentage', 'value' => 10, 'max_discount_amount' => null]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 200])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'coupon' => ['discount_amount' => 20.0, 'final_price' => 180.0],
        ]);
});

it('caps percentage discount at max_discount_amount', function () {
    $listing = makeListing(['base_price' => 500.00]);
    $coupon = activeCoupon(['type' => 'percentage', 'value' => 50, 'max_discount_amount' => 30]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 500])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'coupon' => ['discount_amount' => 30.0, 'final_price' => 470.0],
        ]);
});

it('does not make final price go below zero for fixed discount larger than price', function () {
    $listing = makeListing(['base_price' => 10.00]);
    $coupon = activeCoupon(['type' => 'fixed', 'value' => 200]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 10])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'coupon' => ['discount_amount' => 10.0, 'final_price' => 0.0],
        ]);
});

// ==========================================
// HAPPY PATH — guests and logged-in users
// ==========================================

it('works for guests (no authentication required)', function () {
    $listing = makeListing(['base_price' => 100.00]);
    $coupon = activeCoupon(['type' => 'fixed', 'value' => 20]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 100])
        ->assertOk()
        ->assertJson(['success' => true]);
});

it('returns the coupon code in the response', function () {
    $listing = makeListing();
    $coupon = activeCoupon(['type' => 'fixed', 'value' => 5]);

    postCoupon($listing, ['code' => $coupon->code, 'price' => 100])
        ->assertOk()
        ->assertJsonPath('coupon.code', $coupon->code);
});
