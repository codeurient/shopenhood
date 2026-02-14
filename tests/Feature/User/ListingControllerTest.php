<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create(['current_role' => 'normal_user']);
    $this->actingAs($this->user);

    $this->category = Category::factory()->create();
    $this->listingType = ListingType::factory()->create();
});

test('user can view my listings page', function () {
    $response = $this->get(route('user.listings.index'));

    $response->assertSuccessful();
});

test('user can view create listing form', function () {
    $response = $this->get(route('user.listings.create'));

    $response->assertSuccessful();
});

test('user cannot access create when at limit', function () {
    Listing::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('user.listings.create'));

    $response->assertRedirect(route('user.listings.index'));
    $response->assertSessionHas('error');
});

test('user can create a listing', function () {
    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'My Test Listing',
        'description' => 'A description for my listing',
        'base_price' => 99.99,
        'condition' => 'new',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $this->user->id)->first();
    expect($listing)->not->toBeNull();
    expect($listing->title)->toBe('My Test Listing');
    expect($listing->status)->toBe('pending');
    expect($listing->created_as_role)->toBe('normal_user');
    expect($listing->expires_at)->not->toBeNull();
});

test('user cannot create listing when at limit via store', function () {
    Listing::factory()->create(['user_id' => $this->user->id]);

    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Another Listing',
        'description' => 'Description',
        'condition' => 'new',
    ]);

    $response->assertRedirect(route('user.listings.index'));
    $response->assertSessionHas('error');
});

test('user can edit own listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
    ]);

    $response = $this->get(route('user.listings.edit', $listing));

    $response->assertSuccessful();
});

test('user cannot edit another users listing', function () {
    $otherUser = User::factory()->create();
    $listing = Listing::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->get(route('user.listings.edit', $listing));

    $response->assertForbidden();
});

test('user can update own listing', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
    ]);

    $response = $this->put(route('user.listings.update', $listing), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'condition' => 'new',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing->refresh();
    expect($listing->title)->toBe('Updated Title');
    expect($listing->status)->toBe('pending');
});

test('user can toggle listing visibility', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'is_visible' => true,
    ]);

    $response = $this->patch(route('user.listings.toggle', $listing));

    $response->assertRedirect();

    $listing->refresh();
    expect($listing->is_visible)->toBeFalse();
});

test('user can soft delete own listing', function () {
    $listing = Listing::factory()->create(['user_id' => $this->user->id]);

    $response = $this->delete(route('user.listings.destroy', $listing));

    $response->assertRedirect(route('user.listings.index'));
    expect(Listing::find($listing->id))->toBeNull();
    expect(Listing::withTrashed()->find($listing->id))->not->toBeNull();
});

test('user can reshare a trashed listing', function () {
    $listing = Listing::factory()->create(['user_id' => $this->user->id]);
    $listing->delete();

    $response = $this->post(route('user.listings.reshare', $listing->id));

    $response->assertRedirect(route('user.listings.index'));

    $listing->refresh();
    expect($listing->trashed())->toBeFalse();
    expect($listing->status)->toBe('pending');
});

test('normal user cannot force delete', function () {
    $listing = Listing::factory()->create(['user_id' => $this->user->id]);
    $listing->delete();

    $response = $this->delete(route('user.listings.force-destroy', $listing->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    expect(Listing::withTrashed()->find($listing->id))->not->toBeNull();
});

test('business user can force delete trashed listing', function () {
    $businessUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $this->actingAs($businessUser);

    $listing = Listing::factory()->create(['user_id' => $businessUser->id]);
    $listing->delete();

    $response = $this->delete(route('user.listings.force-destroy', $listing->id));

    $response->assertRedirect(route('user.listings.index'));
    expect(Listing::withTrashed()->find($listing->id))->toBeNull();
});

test('trashed listings show on index page', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'title' => 'Trashed Listing',
    ]);
    $listing->delete();

    $response = $this->get(route('user.listings.index'));

    $response->assertSuccessful();
    $response->assertSee('Trashed Listing');
});

test('expire command works', function () {
    Listing::factory()->create([
        'status' => 'active',
        'expires_at' => now()->subHour(),
    ]);

    $this->artisan('listings:expire')
        ->assertSuccessful()
        ->expectsOutputToContain('Expired 1');
});

test('purge command works', function () {
    Setting::setValue('listing.soft_delete_retention_days', 30, 'integer', 'listing');

    $listing = Listing::factory()->create();
    $listing->delete();

    Listing::withTrashed()
        ->where('id', $listing->id)
        ->update(['deleted_at' => now()->subDays(31)]);

    $this->artisan('listings:purge-deleted')
        ->assertSuccessful()
        ->expectsOutputToContain('Purged 1');
});

test('user can create listing with main image and detail images', function () {
    Storage::fake('public');

    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Listing With Images',
        'description' => 'Description with images',
        'condition' => 'new',
        'main_image' => UploadedFile::fake()->image('main.jpg'),
        'detail_images' => [
            UploadedFile::fake()->image('detail1.jpg'),
            UploadedFile::fake()->image('detail2.jpg'),
        ],
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $this->user->id)->first();
    expect($listing)->not->toBeNull();
    expect($listing->images)->toHaveCount(3);

    $primary = $listing->images->where('is_primary', true);
    expect($primary)->toHaveCount(1);
    expect($primary->first()->sort_order)->toBe(0);

    $details = $listing->images->where('is_primary', false);
    expect($details)->toHaveCount(2);
});

test('user can update listing with new main image', function () {
    Storage::fake('public');

    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
    ]);

    // Add existing primary image
    $listing->images()->create([
        'image_path' => 'listings/old-main.jpg',
        'original_filename' => 'old-main.jpg',
        'file_size' => 1000,
        'mime_type' => 'image/jpeg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $response = $this->put(route('user.listings.update', $listing), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Updated With New Image',
        'description' => 'Updated description',
        'condition' => 'new',
        'main_image' => UploadedFile::fake()->image('new-main.jpg'),
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing->refresh();
    $listing->load('images');

    // Old primary should now be non-primary
    $oldImage = $listing->images->where('image_path', 'listings/old-main.jpg')->first();
    expect($oldImage->is_primary)->toBeFalse();

    // New primary should exist
    $newPrimary = $listing->images->where('is_primary', true)->first();
    expect($newPrimary)->not->toBeNull();
    expect($newPrimary->sort_order)->toBe(0);
});

test('user can delete images during update', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
    ]);

    $image = $listing->images()->create([
        'image_path' => 'listings/to-delete.jpg',
        'original_filename' => 'to-delete.jpg',
        'file_size' => 1000,
        'mime_type' => 'image/jpeg',
        'sort_order' => 1,
        'is_primary' => false,
    ]);

    $response = $this->put(route('user.listings.update', $listing), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => $listing->title,
        'description' => $listing->description,
        'condition' => 'new',
        'delete_images' => [$image->id],
    ]);

    $response->assertRedirect(route('user.listings.index'));

    expect($listing->images()->count())->toBe(0);
});

test('business user can set store name on listing', function () {
    $businessUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $this->actingAs($businessUser);

    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Business Listing',
        'description' => 'From my store',
        'condition' => 'new',
        'store_name' => 'My Awesome Store',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $businessUser->id)->first();
    expect($listing->store_name)->toBe('My Awesome Store');
});

test('normal user store name is ignored', function () {
    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Normal User Listing',
        'description' => 'Description',
        'condition' => 'new',
        'store_name' => 'Should Be Ignored',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $this->user->id)->first();
    expect($listing->store_name)->toBeNull();
});

test('public category children api returns root categories', function () {
    $parent = Category::factory()->create([
        'parent_id' => null,
        'is_active' => true,
        'name' => 'Root Category',
    ]);

    $response = $this->getJson(route('api.categories.children'));

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);
    $response->assertJsonFragment(['name' => 'Root Category']);
});

test('public category children api returns child categories', function () {
    $parent = Category::factory()->create([
        'parent_id' => null,
        'is_active' => true,
        'name' => 'Parent',
    ]);

    $child = Category::factory()->create([
        'parent_id' => $parent->id,
        'is_active' => true,
        'name' => 'Child Category',
    ]);

    $response = $this->getJson(route('api.categories.children', $parent));

    $response->assertSuccessful();
    $response->assertJsonPath('success', true);
    $response->assertJsonFragment(['name' => 'Child Category']);
});

test('public category children api excludes inactive categories', function () {
    Category::factory()->create([
        'parent_id' => null,
        'is_active' => false,
        'name' => 'Inactive Category',
    ]);

    $response = $this->getJson(route('api.categories.children'));

    $response->assertSuccessful();
    $response->assertJsonMissing(['name' => 'Inactive Category']);
});

test('user can create listing with discount fields', function () {
    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Discounted Item',
        'description' => 'A discounted listing',
        'condition' => 'new',
        'base_price' => 100.00,
        'discount_price' => 79.99,
        'discount_start_date' => '2026-03-01 00:00:00',
        'discount_end_date' => '2026-03-31 23:59:59',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $this->user->id)->first();
    expect($listing->discount_price)->toBe('79.99');
    expect($listing->discount_start_date)->not->toBeNull();
    expect($listing->discount_end_date)->not->toBeNull();
});

test('business user can create listing with availability type', function () {
    $businessUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $this->actingAs($businessUser);

    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Available By Order Item',
        'description' => 'An on-demand listing',
        'condition' => 'new',
        'availability_type' => 'available_by_order',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $businessUser->id)->first();
    expect($listing->availability_type)->toBe('available_by_order');
});

test('normal user availability type is ignored', function () {
    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Normal User Listing',
        'description' => 'Description',
        'condition' => 'new',
        'availability_type' => 'available_by_order',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $this->user->id)->first();
    expect($listing->availability_type)->toBe('in_stock'); // Default value, not the submitted one
});

test('user can create listing with delivery options', function () {
    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'Delivered Item',
        'description' => 'A listing with delivery',
        'condition' => 'new',
        'base_price' => 50.00,
        'has_delivery' => '1',
        'has_domestic_delivery' => '1',
        'domestic_delivery_price' => 5.99,
        'has_international_delivery' => '1',
        'international_delivery_price' => 19.99,
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $this->user->id)->first();
    expect($listing->has_delivery)->toBeTrue();
    expect($listing->has_domestic_delivery)->toBeTrue();
    expect($listing->domestic_delivery_price)->toBe('5.99');
    expect($listing->has_international_delivery)->toBeTrue();
    expect($listing->international_delivery_price)->toBe('19.99');
});

test('delivery defaults to false when checkbox not submitted', function () {
    $response = $this->post(route('user.listings.store'), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => 'No Delivery Item',
        'description' => 'A listing without delivery',
        'condition' => 'new',
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing = Listing::where('user_id', $this->user->id)->first();
    expect($listing->has_delivery)->toBeFalse();
    expect($listing->has_domestic_delivery)->toBeFalse();
    expect($listing->has_international_delivery)->toBeFalse();
});

test('user can update listing with delivery options', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'listing_type_id' => $this->listingType->id,
        'has_delivery' => false,
    ]);

    $response = $this->put(route('user.listings.update', $listing), [
        'listing_type_id' => $this->listingType->id,
        'category_id' => $this->category->id,
        'title' => $listing->title,
        'description' => $listing->description,
        'condition' => 'new',
        'has_delivery' => '1',
        'has_domestic_delivery' => '1',
        'domestic_delivery_price' => 8.50,
    ]);

    $response->assertRedirect(route('user.listings.index'));

    $listing->refresh();
    expect($listing->has_delivery)->toBeTrue();
    expect($listing->has_domestic_delivery)->toBeTrue();
    expect($listing->domestic_delivery_price)->toBe('8.50');
    expect($listing->has_international_delivery)->toBeFalse();
});

test('edit page passes category chain for pre-selection', function () {
    $parent = Category::factory()->create(['parent_id' => null, 'is_active' => true]);
    $child = Category::factory()->create(['parent_id' => $parent->id, 'is_active' => true]);

    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $child->id,
    ]);

    $response = $this->get(route('user.listings.edit', $listing));

    $response->assertSuccessful();
    $response->assertViewHas('categoryChain');

    $chain = $response->viewData('categoryChain');
    expect($chain)->toHaveCount(2);
    expect($chain[0]['id'])->toBe($parent->id);
    expect($chain[1]['id'])->toBe($child->id);
});
