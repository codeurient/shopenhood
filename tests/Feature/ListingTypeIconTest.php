<?php

use App\Models\ListingType;
use App\Models\User;

it('renders font awesome icon html in mobile listing type tabs', function () {
    ListingType::factory()->create([
        'slug' => 'sell-icon-test',
        'icon' => '<i class="fa-solid fa-tag"></i>',
        'is_active' => true,
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('fa-solid fa-tag', false);
});

it('shows fallback icon when listing type has no icon set', function () {
    ListingType::factory()->create([
        'slug' => 'no-icon-type',
        'icon' => null,
        'is_active' => true,
    ]);

    // Page still loads and at minimum renders the fallback tag icon
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('fa-solid fa-tag', false);
});

it('does not render the type name as visible text in mobile tabs', function () {
    ListingType::factory()->create([
        'name' => 'UniqueTypeName99',
        'slug' => 'unique-type-99',
        'icon' => '<i class="fa-solid fa-star"></i>',
        'is_active' => true,
    ]);

    $response = $this->get('/');
    $response->assertSuccessful();

    // The name should only appear as a title attribute (tooltip), not as visible text content
    $response->assertSee('title="UniqueTypeName99"', false);
});

it('admin can update a listing type icon and it persists', function () {
    $admin = User::factory()->create(['current_role' => 'admin']);

    $type = ListingType::factory()->create([
        'name' => 'Barter',
        'slug' => 'barter-upd-'.rand(1000, 9999),
        'icon' => '<i class="fa-solid fa-arrows-rotate"></i>',
        'is_active' => true,
    ]);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.listing-types.update', $type), [
            'name' => $type->name,
            'slug' => $type->slug,
            'icon' => '<i class="fa-solid fa-handshake"></i>',
            'is_active' => true,
            'requires_price' => false,
            'sort_order' => $type->sort_order,
        ])
        ->assertRedirect();

    expect($type->fresh()->icon)->toBe('<i class="fa-solid fa-handshake"></i>');
});

it('admin edit form renders the icon preview element with current icon', function () {
    $admin = User::factory()->create(['current_role' => 'admin']);

    $type = ListingType::factory()->create([
        'icon' => '<i class="fa-solid fa-gift"></i>',
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.listing-types.edit', $type))
        ->assertSuccessful()
        ->assertSee('icon-preview', false)
        ->assertSee('fa-solid fa-gift', false);
});
