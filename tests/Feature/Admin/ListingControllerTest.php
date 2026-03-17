<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use App\Models\Variant;
use App\Models\VariantItem;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

// Admin cannot create listings — they must register as a normal user to post listings.
// The create/store/edit/update routes are intentionally removed from the admin panel.

test('admin cannot access listing create page', function () {
    $response = $this->get('/admin/listings/create');
    $response->assertNotFound();
});

test('admin cannot post to listing store endpoint', function () {
    $response = $this->post('/admin/listings', ['title' => 'Test']);
    $response->assertStatus(405); // Method Not Allowed — store route removed
});

test('admin cannot access listing edit page', function () {
    $listing = Listing::factory()->create();
    $response = $this->get("/admin/listings/{$listing->id}/edit");
    $response->assertNotFound();
});

test('admin cannot put to listing update endpoint', function () {
    $listing = Listing::factory()->create();
    $response = $this->put("/admin/listings/{$listing->id}", ['title' => 'Updated']);
    $response->assertStatus(405); // Method Not Allowed — update route removed
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

test('admin can bulk soft-delete selected listings', function () {
    $listing1 = Listing::factory()->create(['status' => 'active']);
    $listing2 = Listing::factory()->create(['status' => 'active']);
    $listing3 = Listing::factory()->create(['status' => 'active']);

    $response = $this->post(route('admin.listings.bulk-delete'), ['ids' => [$listing1->id, $listing2->id]]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertSoftDeleted('listings', ['id' => $listing1->id]);
    $this->assertSoftDeleted('listings', ['id' => $listing2->id]);
    $this->assertDatabaseHas('listings', ['id' => $listing3->id, 'deleted_at' => null]);
});

test('admin bulk delete returns error when no ids given', function () {
    $response = $this->post(route('admin.listings.bulk-delete'), ['ids' => []]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin can bulk permanently delete selected trashed listings and files are cleaned up', function () {
    Storage::fake('public');

    $listing1 = Listing::factory()->create();
    $listing2 = Listing::factory()->create();

    $image1 = $listing1->images()->create([
        'image_path' => 'listings/test1.jpg',
        'original_filename' => 'test1.jpg',
        'file_size' => 1024,
        'mime_type' => 'image/jpeg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);
    Storage::disk('public')->put('listings/test1.jpg', 'fake');

    $listing1->delete();
    $listing2->delete();

    $response = $this->post(route('admin.listings.bulk-force-destroy'), ['ids' => [$listing1->id, $listing2->id]]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('listings', ['id' => $listing1->id]);
    $this->assertDatabaseMissing('listings', ['id' => $listing2->id]);
    Storage::disk('public')->assertMissing('listings/test1.jpg');
});

test('admin bulk force destroy returns error when no ids given', function () {
    $response = $this->post(route('admin.listings.bulk-force-destroy'), ['ids' => []]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin force delete uses service to clean up listing image files', function () {
    Storage::fake('public');

    $listing = Listing::factory()->create();
    $listing->images()->create([
        'image_path' => 'listings/admin-test.jpg',
        'original_filename' => 'admin-test.jpg',
        'file_size' => 1024,
        'mime_type' => 'image/jpeg',
        'sort_order' => 0,
        'is_primary' => true,
    ]);
    Storage::disk('public')->put('listings/admin-test.jpg', 'fake');

    $listing->delete();

    $this->delete(route('admin.listings.force-delete', $listing->id));

    Storage::disk('public')->assertMissing('listings/admin-test.jpg');
    $this->assertDatabaseMissing('listings', ['id' => $listing->id]);
});
