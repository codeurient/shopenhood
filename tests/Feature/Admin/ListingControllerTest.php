<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\Variant;
use App\Models\VariantItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can create a listing with required fields', function () {
    Storage::fake('public');

    $listingType = ListingType::factory()->create(['name' => 'Sell', 'requires_price' => true]);
    $category = Category::factory()->create(['name' => 'Electronics']);

    $data = [
        'listing_type_id' => $listingType->id,
        'category_id' => $category->id,
        'title' => 'Test Product',
        'slug' => 'test-product',
        'description' => 'This is a test product description',
        'short_description' => 'Test product summary',
        'base_price' => 99.99,
        'currency' => 'USD',
        'status' => 'active',
        'availability_type' => 'in_stock',
        'country' => 'United States',
        'city' => 'New York',
        'created_as_role' => 'admin',
    ];

    $response = $this->post(route('admin.listings.store'), $data);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('listings', [
        'title' => 'Test Product',
        'slug' => 'test-product',
        'listing_type_id' => $listingType->id,
        'category_id' => $category->id,
        'short_description' => 'Test product summary',
        'base_price' => 99.99,
        'availability_type' => 'in_stock',
        'country' => 'United States',
        'city' => 'New York',
    ]);
});

test('admin can create a listing with discount pricing', function () {
    Storage::fake('public');

    $listingType = ListingType::factory()->create(['name' => 'Sell', 'requires_price' => true]);
    $category = Category::factory()->create(['name' => 'Electronics']);

    $data = [
        'listing_type_id' => $listingType->id,
        'category_id' => $category->id,
        'title' => 'Discounted Product',
        'description' => 'Product with discount',
        'base_price' => 100.00,
        'discount_price' => 79.99,
        'discount_start_date' => now()->format('Y-m-d H:i:s'),
        'discount_end_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
        'status' => 'active',
    ];

    $response = $this->post(route('admin.listings.store'), $data);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('listings', [
        'title' => 'Discounted Product',
        'base_price' => 100.00,
        'discount_price' => 79.99,
    ]);

    $listing = Listing::where('title', 'Discounted Product')->first();
    expect($listing->discount_start_date)->not->toBeNull();
    expect($listing->discount_end_date)->not->toBeNull();
});

test('admin can create a listing with store name for business user', function () {
    Storage::fake('public');

    $listingType = ListingType::factory()->create(['name' => 'Sell']);
    $category = Category::factory()->create(['name' => 'Electronics']);

    $data = [
        'listing_type_id' => $listingType->id,
        'category_id' => $category->id,
        'title' => 'Business Product',
        'description' => 'Product from business',
        'base_price' => 50.00,
        'status' => 'active',
        'created_as_role' => 'business_user',
        'store_name' => 'My Test Store',
    ];

    $response = $this->post(route('admin.listings.store'), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('listings', [
        'title' => 'Business Product',
        'created_as_role' => 'business_user',
        'store_name' => 'My Test Store',
    ]);
});

test('admin can create a listing with images', function () {
    Storage::fake('public');

    $listingType = ListingType::factory()->create(['name' => 'Sell']);
    $category = Category::factory()->create(['name' => 'Electronics']);

    $mainImage = UploadedFile::fake()->image('main.jpg');
    $detailImage1 = UploadedFile::fake()->image('detail1.jpg');
    $detailImage2 = UploadedFile::fake()->image('detail2.jpg');

    $data = [
        'listing_type_id' => $listingType->id,
        'category_id' => $category->id,
        'title' => 'Product with Images',
        'description' => 'Product description',
        'base_price' => 75.00,
        'status' => 'active',
        'images' => [$mainImage],
        'detail_images' => [$detailImage1, $detailImage2],
    ];

    $response = $this->post(route('admin.listings.store'), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('listings', [
        'title' => 'Product with Images',
    ]);

    $listing = Listing::where('title', 'Product with Images')->first();
    expect($listing->images)->toHaveCount(3);
});

test('listing creation requires category_id', function () {
    $listingType = ListingType::factory()->create();

    $data = [
        'listing_type_id' => $listingType->id,
        'title' => 'Test Product',
        'description' => 'Description',
        'status' => 'active',
    ];

    $response = $this->post(route('admin.listings.store'), $data);

    $response->assertSessionHasErrors('category_id');
});

test('discount price must be less than base price', function () {
    $listingType = ListingType::factory()->create();
    $category = Category::factory()->create();

    $data = [
        'listing_type_id' => $listingType->id,
        'category_id' => $category->id,
        'title' => 'Invalid Discount',
        'description' => 'Description',
        'base_price' => 50.00,
        'discount_price' => 60.00,
        'status' => 'active',
    ];

    $response = $this->post(route('admin.listings.store'), $data);

    $response->assertSessionHasErrors('discount_price');
});

test('updating a listing without variations deletes all existing variations', function () {
    $listing = Listing::factory()->create(['user_id' => $this->admin->id]);

    // Create some variations for the listing
    ProductVariation::factory()->count(3)->create(['listing_id' => $listing->id]);

    expect($listing->variations()->count())->toBe(3);

    $data = [
        'listing_type_id' => $listing->listing_type_id,
        'category_id' => $listing->category_id,
        'title' => $listing->title,
        'description' => $listing->description,
        'status' => $listing->status,
        // No 'variations' key - user deleted all of them
    ];

    $response = $this->put(route('admin.listings.update', $listing), $data);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($listing->fresh()->variations()->count())->toBe(0);
});

test('updating a listing with variations keeps provided ones and deletes removed ones', function () {
    $listing = Listing::factory()->create(['user_id' => $this->admin->id]);

    $keepVariation = ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'sku' => 'KEEP-001',
        'price' => 50.00,
    ]);
    ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'sku' => 'DELETE-001',
    ]);
    ProductVariation::factory()->create([
        'listing_id' => $listing->id,
        'sku' => 'DELETE-002',
    ]);

    expect($listing->variations()->count())->toBe(3);

    $data = [
        'listing_type_id' => $listing->listing_type_id,
        'category_id' => $listing->category_id,
        'title' => $listing->title,
        'description' => $listing->description,
        'status' => $listing->status,
        'variations' => [
            [
                'id' => $keepVariation->id,
                'sku' => 'KEEP-001',
                'price' => 55.00,
            ],
        ],
    ];

    $response = $this->put(route('admin.listings.update', $listing), $data);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $remaining = $listing->fresh()->variations;
    expect($remaining)->toHaveCount(1);
    expect($remaining->first()->sku)->toBe('KEEP-001');
    expect((float) $remaining->first()->price)->toBe(55.00);
});

test('getCategoryVariants returns variants assigned to a category', function () {
    $category = Category::factory()->create();
    $variant = Variant::factory()->create(['name' => 'Color']);
    VariantItem::factory()->count(2)->create(['variant_id' => $variant->id]);

    // Attach variant to category with is_main_shown = true
    $category->variants()->attach($variant->id, [
        'is_required' => true,
        'is_searchable' => false,
        'is_filterable' => false,
        'is_main_shown' => true,
        'sort_order' => 0,
    ]);

    $response = $this->getJson(route('admin.listings.category.variants', $category));

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);
    $response->assertJsonCount(1, 'variants');
    $response->assertJsonPath('variants.0.name', 'Color');
    $response->assertJsonPath('variants.0.is_main_shown', 1);
});

// ==========================================
// SOFT DELETE VISIBILITY
// ==========================================

test('admin can filter listings by deleted status', function () {
    $listing = Listing::factory()->create();
    $listing->delete();

    $response = $this->get(route('admin.listings.index', ['status' => 'deleted']));

    $response->assertSuccessful();
    $response->assertSee($listing->title);
});

test('deleted listings do not appear in default index', function () {
    $active = Listing::factory()->create(['title' => 'Active Listing']);
    $deleted = Listing::factory()->create(['title' => 'Deleted Listing']);
    $deleted->delete();

    $response = $this->get(route('admin.listings.index'));

    $response->assertSee('Active Listing');
    $response->assertDontSee('Deleted Listing');
});

test('admin index shows deleted count in stats', function () {
    Listing::factory()->create();
    $deleted = Listing::factory()->create();
    $deleted->delete();

    $response = $this->get(route('admin.listings.index'));

    $response->assertSuccessful();
    $response->assertSee('Deleted');
});

test('admin can restore a trashed listing', function () {
    $listing = Listing::factory()->create(['status' => 'active']);
    $listing->delete();

    $response = $this->post(route('admin.listings.restore', $listing->id));

    $response->assertRedirect(route('admin.listings.index'));
    $response->assertSessionHas('success');

    $listing->refresh();
    expect($listing->deleted_at)->toBeNull();
    expect($listing->status)->toBe('pending');
});

test('admin can permanently delete a trashed listing', function () {
    $listing = Listing::factory()->create();
    $listingId = $listing->id;
    $listing->delete();

    $response = $this->delete(route('admin.listings.force-delete', $listingId));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(Listing::withTrashed()->find($listingId))->toBeNull();
});

test('deleted filter shows restore and permanently delete buttons', function () {
    $listing = Listing::factory()->create();
    $listing->delete();

    $response = $this->get(route('admin.listings.index', ['status' => 'deleted']));

    $response->assertSuccessful();
    $response->assertSee('Restore');
    $response->assertSee('Permanently Delete');
});

// ==========================================
// CATEGORY VARIANTS
// ==========================================

test('getCategoryVariants with show_all returns all variants including non-main-shown', function () {
    $category = Category::factory()->create();
    $mainVariant = Variant::factory()->create(['name' => 'Color']);
    $otherVariant = Variant::factory()->create(['name' => 'Size']);

    $category->variants()->attach($mainVariant->id, [
        'is_required' => true,
        'is_searchable' => false,
        'is_filterable' => false,
        'is_main_shown' => true,
        'sort_order' => 0,
    ]);
    $category->variants()->attach($otherVariant->id, [
        'is_required' => false,
        'is_searchable' => false,
        'is_filterable' => false,
        'is_main_shown' => false,
        'sort_order' => 1,
    ]);

    // Without show_all - only main shown
    $response = $this->getJson(route('admin.listings.category.variants', $category));
    $response->assertSuccessful();
    $response->assertJsonCount(1, 'variants');

    // With show_all - all variants
    $response = $this->getJson(route('admin.listings.category.variants', ['category' => $category, 'show_all' => 'true']));
    $response->assertSuccessful();
    $response->assertJsonCount(2, 'variants');
});
