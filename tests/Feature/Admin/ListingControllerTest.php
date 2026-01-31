<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\User;
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
