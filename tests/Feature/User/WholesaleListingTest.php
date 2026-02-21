<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\VariationPriceTier;

beforeEach(function () {
    $this->businessUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $this->normalUser = User::factory()->create([
        'current_role' => 'normal_user',
    ]);

    $this->category = Category::factory()->create(['is_active' => true]);
    $this->listingType = ListingType::factory()->create(['is_active' => true]);
});

// ==========================================
// WHOLESALE FIELD ACCESS
// ==========================================

test('business user can create listing with wholesale enabled on a variation', function () {
    $this->actingAs($this->businessUser);

    // Business users now set wholesale per-variation, not at the listing level.
    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Wholesale Test Product',
        'description' => 'A product for wholesale.',
        'condition' => 'new',
        'variations' => [
            [
                'sku' => 'WHOLESALE-001',
                'price' => 50.00,
                'stock_quantity' => 100,
                'is_wholesale' => 1,
                'wholesale_min_order_qty' => 10,
                'wholesale_qty_increment' => 5,
                'wholesale_lead_time_days' => 7,
                'wholesale_sample_available' => 1,
                'wholesale_sample_price' => 5.00,
                'wholesale_terms' => 'Payment within 30 days.',
            ],
        ],
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertRedirect(route('business.listings.index'));

    $listing = \App\Models\Listing::where('title', 'Wholesale Test Product')->first();
    expect($listing)->not->toBeNull();

    $variation = $listing->variations()->first();
    expect($variation->is_wholesale)->toBeTrue();
    expect($variation->wholesale_min_order_qty)->toBe(10);
    expect($variation->wholesale_qty_increment)->toBe(5);
    expect($variation->wholesale_lead_time_days)->toBe(7);
    expect($variation->wholesale_sample_available)->toBeTrue();
    expect((float) $variation->wholesale_sample_price)->toBe(5.0);
    expect($variation->wholesale_terms)->toBe('Payment within 30 days.');
});

test('normal user cannot enable wholesale on listing', function () {
    $this->actingAs($this->normalUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Normal User Product',
        'description' => 'A product from normal user.',
        'condition' => 'new',
        'base_price' => 25.00,
        'is_wholesale' => true,
        'wholesale_min_order_qty' => 10,
    ];

    $response = $this->post(route('user.listings.store'), $data);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('title', 'Normal User Product')->first();
    expect($listing)->not->toBeNull();
    expect($listing->is_wholesale)->toBeFalse();
    expect($listing->wholesale_min_order_qty)->toBeNull();
});

test('business user can update listing with wholesale fields on a variation', function () {
    $this->actingAs($this->businessUser);

    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
        'listing_mode' => 'business',
        'is_wholesale' => false,
        'status' => 'active',
    ]);

    $variation = ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'is_wholesale' => false,
    ]);

    // Business users now set wholesale per-variation, not at the listing level.
    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => $listing->title,
        'description' => $listing->description,
        'condition' => 'new',
        'variations' => [
            [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'price' => (float) $variation->price,
                'stock_quantity' => $variation->stock_quantity,
                'is_wholesale' => 1,
                'wholesale_min_order_qty' => 20,
                'wholesale_lead_time_days' => 14,
            ],
        ],
    ];

    $response = $this->put(route('business.listings.update', $listing), $data);

    $response->assertRedirect(route('business.listings.index'));

    $variation->refresh();
    expect($variation->is_wholesale)->toBeTrue();
    expect($variation->wholesale_min_order_qty)->toBe(20);
    expect($variation->wholesale_lead_time_days)->toBe(14);
});

// ==========================================
// WHOLESALE SCOPE
// ==========================================

test('wholesale scope filters listings correctly', function () {
    Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'is_wholesale' => true,
        'title' => 'Wholesale Product',
    ]);

    Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'is_wholesale' => false,
        'title' => 'Retail Product',
    ]);

    $wholesaleListings = Listing::wholesale()->get();

    expect($wholesaleListings)->toHaveCount(1);
    expect($wholesaleListings->first()->title)->toBe('Wholesale Product');
});

test('isWholesale helper returns correct value', function () {
    $wholesaleListing = Listing::factory()->create(['is_wholesale' => true]);
    $retailListing = Listing::factory()->create(['is_wholesale' => false]);

    expect($wholesaleListing->isWholesale())->toBeTrue();
    expect($retailListing->isWholesale())->toBeFalse();
});

// ==========================================
// PRICE TIERS
// ==========================================

test('price tiers can be created for variation', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'is_wholesale' => true,
    ]);

    $variation = ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'price' => 10.00,
    ]);

    VariationPriceTier::create([
        'product_variation_id' => $variation->id,
        'min_quantity' => 1,
        'max_quantity' => 9,
        'unit_price' => 10.00,
    ]);

    VariationPriceTier::create([
        'product_variation_id' => $variation->id,
        'min_quantity' => 10,
        'max_quantity' => 49,
        'unit_price' => 8.50,
    ]);

    VariationPriceTier::create([
        'product_variation_id' => $variation->id,
        'min_quantity' => 50,
        'max_quantity' => null,
        'unit_price' => 7.00,
    ]);

    expect($variation->priceTiers)->toHaveCount(3);
});

test('getPriceForQuantity returns correct tier price', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'is_wholesale' => true,
    ]);

    $variation = ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'price' => 10.00,
    ]);

    VariationPriceTier::create([
        'product_variation_id' => $variation->id,
        'min_quantity' => 1,
        'max_quantity' => 9,
        'unit_price' => 10.00,
    ]);

    VariationPriceTier::create([
        'product_variation_id' => $variation->id,
        'min_quantity' => 10,
        'max_quantity' => 49,
        'unit_price' => 8.50,
    ]);

    VariationPriceTier::create([
        'product_variation_id' => $variation->id,
        'min_quantity' => 50,
        'max_quantity' => null,
        'unit_price' => 7.00,
    ]);

    expect($variation->getPriceForQuantity(5))->toBe(10.00);
    expect($variation->getPriceForQuantity(10))->toBe(8.50);
    expect($variation->getPriceForQuantity(25))->toBe(8.50);
    expect($variation->getPriceForQuantity(50))->toBe(7.00);
    expect($variation->getPriceForQuantity(100))->toBe(7.00);
});

test('getPriceForQuantity returns base price when no tiers exist', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'is_wholesale' => true,
    ]);

    $variation = ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'price' => 15.00,
    ]);

    expect($variation->getPriceForQuantity(10))->toBe(15.00);
});

test('hasTieredPricing returns correct value', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'is_wholesale' => true,
    ]);

    $variationWithTiers = ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'price' => 10.00,
    ]);

    $variationWithoutTiers = ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'price' => 10.00,
    ]);

    VariationPriceTier::create([
        'product_variation_id' => $variationWithTiers->id,
        'min_quantity' => 10,
        'unit_price' => 8.00,
    ]);

    expect($variationWithTiers->hasTieredPricing())->toBeTrue();
    expect($variationWithoutTiers->hasTieredPricing())->toBeFalse();
});

test('price tiers are deleted when variation is deleted', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'is_wholesale' => true,
    ]);

    $variation = ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'price' => 10.00,
    ]);

    $tier = VariationPriceTier::create([
        'product_variation_id' => $variation->id,
        'min_quantity' => 10,
        'unit_price' => 8.00,
    ]);

    $variation->delete();

    $this->assertDatabaseMissing('variation_price_tiers', ['id' => $tier->id]);
});

// ==========================================
// VALIDATION
// ==========================================

test('per-variation wholesale min order qty must be positive', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Test Product',
        'description' => 'Test description.',
        'condition' => 'new',
        'variations' => [
            [
                'sku' => 'TEST-001',
                'price' => 10.00,
                'is_wholesale' => 1,
                'wholesale_min_order_qty' => 0,
            ],
        ],
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertSessionHasErrors('variations.0.wholesale_min_order_qty');
});

test('per-variation wholesale lead time days cannot exceed 365', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Test Product',
        'description' => 'Test description.',
        'condition' => 'new',
        'variations' => [
            [
                'sku' => 'TEST-001',
                'price' => 10.00,
                'is_wholesale' => 1,
                'wholesale_min_order_qty' => 10,
                'wholesale_lead_time_days' => 500,
            ],
        ],
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertSessionHasErrors('variations.0.wholesale_lead_time_days');
});

test('per-variation wholesale terms cannot exceed 2000 characters', function () {
    $this->actingAs($this->businessUser);

    $data = [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Test Product',
        'description' => 'Test description.',
        'condition' => 'new',
        'variations' => [
            [
                'sku' => 'TEST-001',
                'price' => 10.00,
                'is_wholesale' => 1,
                'wholesale_min_order_qty' => 10,
                'wholesale_terms' => str_repeat('a', 2001),
            ],
        ],
    ];

    $response = $this->post(route('business.listings.store'), $data);

    $response->assertSessionHasErrors('variations.0.wholesale_terms');
});
