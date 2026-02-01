<?php

use App\Models\User;
use App\Models\Variant;
use App\Models\VariantItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
    $this->variant = Variant::factory()->create();
});

test('admin can view variant item edit page', function () {
    $item = VariantItem::factory()->create(['variant_id' => $this->variant->id]);

    $response = $this->get(route('admin.variants.items.edit', [$this->variant, $item]));

    $response->assertSuccessful();
    $response->assertSee($item->value);
});

test('admin can update a variant item', function () {
    $item = VariantItem::factory()->create([
        'variant_id' => $this->variant->id,
        'value' => 'Old Value',
    ]);

    $response = $this->put(route('admin.variants.items.update', [$this->variant, $item]), [
        'value' => 'New Value',
        'display_value' => 'New Display',
        'sort_order' => 5,
    ]);

    $response->assertRedirect(route('admin.variants.items.index', $this->variant));
    $response->assertSessionHas('success');

    $item->refresh();
    expect($item->value)->toBe('New Value');
    expect($item->display_value)->toBe('New Display');
    expect($item->sort_order)->toBe(5);
});

test('variant item update validates required value', function () {
    $item = VariantItem::factory()->create(['variant_id' => $this->variant->id]);

    $response = $this->put(route('admin.variants.items.update', [$this->variant, $item]), [
        'value' => '',
    ]);

    $response->assertSessionHasErrors('value');
});

test('variant item update rejects duplicate value', function () {
    VariantItem::factory()->create([
        'variant_id' => $this->variant->id,
        'value' => 'Existing Value',
    ]);

    $item = VariantItem::factory()->create([
        'variant_id' => $this->variant->id,
        'value' => 'Original Value',
    ]);

    $response = $this->put(route('admin.variants.items.update', [$this->variant, $item]), [
        'value' => 'Existing Value',
    ]);

    $response->assertSessionHasErrors('value');
});

test('variant item update allows keeping own value', function () {
    $item = VariantItem::factory()->create([
        'variant_id' => $this->variant->id,
        'value' => 'My Value',
    ]);

    $response = $this->put(route('admin.variants.items.update', [$this->variant, $item]), [
        'value' => 'My Value',
    ]);

    $response->assertRedirect(route('admin.variants.items.index', $this->variant));
    $response->assertSessionDoesntHaveErrors();
});

test('variant item update with image upload', function () {
    Storage::fake('public');

    $item = VariantItem::factory()->create(['variant_id' => $this->variant->id]);

    $response = $this->put(route('admin.variants.items.update', [$this->variant, $item]), [
        'value' => $item->value,
        'image' => UploadedFile::fake()->image('swatch.png', 100, 100),
    ]);

    $response->assertRedirect(route('admin.variants.items.index', $this->variant));

    $item->refresh();
    expect($item->image)->not->toBeNull();
    Storage::disk('public')->assertExists($item->image);
});

test('admin can delete a variant item', function () {
    $item = VariantItem::factory()->create(['variant_id' => $this->variant->id]);
    $itemId = $item->id;

    $response = $this->delete(route('admin.variants.items.destroy', [$this->variant, $item]));

    $response->assertRedirect(route('admin.variants.items.index', $this->variant));
    $response->assertSessionHas('success');

    expect(VariantItem::find($itemId))->toBeNull();
});
