<?php

use App\Models\User;
use App\Models\Variant;
use App\Models\VariantItem;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can view variant edit page', function () {
    $variant = Variant::factory()->create();

    $response = $this->get(route('admin.variants.edit', $variant));

    $response->assertSuccessful();
    $response->assertSee($variant->name);
});

test('admin can update a variant', function () {
    $variant = Variant::factory()->create(['name' => 'Old Color', 'slug' => 'old-color']);

    $response = $this->put(route('admin.variants.update', $variant), [
        'name' => 'Updated Color',
        'slug' => 'updated-color',
        'type' => 'radio',
        'description' => 'Pick a color',
        'sort_order' => 3,
    ]);

    $response->assertRedirect(route('admin.variants.index'));
    $response->assertSessionHas('success');

    $variant->refresh();
    expect($variant->name)->toBe('Updated Color');
    expect($variant->slug)->toBe('updated-color');
    expect($variant->type)->toBe('radio');
    expect($variant->description)->toBe('Pick a color');
    expect($variant->sort_order)->toBe(3);
});

test('variant update validates required fields', function () {
    $variant = Variant::factory()->create();

    $response = $this->put(route('admin.variants.update', $variant), [
        'name' => '',
        'type' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'type']);
});

test('variant update validates unique name', function () {
    $existing = Variant::factory()->create(['name' => 'Taken Name']);
    $variant = Variant::factory()->create();

    $response = $this->put(route('admin.variants.update', $variant), [
        'name' => 'Taken Name',
        'type' => $variant->type,
    ]);

    $response->assertSessionHasErrors('name');
});

test('variant update allows keeping own name', function () {
    $variant = Variant::factory()->create(['name' => 'My Variant']);

    $response = $this->put(route('admin.variants.update', $variant), [
        'name' => 'My Variant',
        'type' => $variant->type,
    ]);

    $response->assertRedirect(route('admin.variants.index'));
    $response->assertSessionDoesntHaveErrors();
});

test('variant update validates type enum', function () {
    $variant = Variant::factory()->create();

    $response = $this->put(route('admin.variants.update', $variant), [
        'name' => $variant->name,
        'type' => 'invalid-type',
    ]);

    $response->assertSessionHasErrors('type');
});

test('admin can delete a variant', function () {
    $variant = Variant::factory()->create();
    $variantId = $variant->id;

    $response = $this->delete(route('admin.variants.destroy', $variant));

    $response->assertRedirect(route('admin.variants.index'));
    $response->assertSessionHas('success');

    expect(Variant::find($variantId))->toBeNull();
});

test('deleting a variant cascades to its items', function () {
    $variant = Variant::factory()->create();
    $item = VariantItem::factory()->create(['variant_id' => $variant->id]);

    $this->delete(route('admin.variants.destroy', $variant));

    expect(Variant::find($variant->id))->toBeNull();
    expect(VariantItem::find($item->id))->toBeNull();
});
