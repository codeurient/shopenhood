<?php

use App\Models\Category;
use App\Models\Listing;

it('returns empty array when query is less than 2 characters', function () {
    $this->getJson('/search/suggestions?q=a')
        ->assertOk()
        ->assertJson([]);

    $this->getJson('/search/suggestions?q=')
        ->assertOk()
        ->assertJson([]);
});

it('returns matching listings for a valid query', function () {
    Listing::factory()->create([
        'title' => 'Red Leather Jacket',
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
    ]);

    $data = $this->getJson('/search/suggestions?q=Leather')->assertOk()->json();

    expect(collect($data)->pluck('title'))->toContain('Red Leather Jacket');
});

it('returns suggestions with required fields', function () {
    Listing::factory()->create([
        'title' => 'Blue Running Shoes',
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
    ]);

    $data = $this->getJson('/search/suggestions?q=Running')->assertOk()->json();

    expect($data)->not->toBeEmpty();
    expect($data[0])->toHaveKeys(['title', 'url', 'image_url', 'category_name', 'base_price', 'currency']);
});

it('does not return draft or hidden listings', function () {
    Listing::factory()->create([
        'title' => 'Draft Widget',
        'status' => 'draft',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
    ]);

    Listing::factory()->create([
        'title' => 'Invisible Widget',
        'status' => 'active',
        'is_visible' => false,
        'hidden_due_to_role_change' => false,
    ]);

    $titles = collect($this->getJson('/search/suggestions?q=Widget')->assertOk()->json())->pluck('title');

    expect($titles)->not->toContain('Draft Widget')
        ->and($titles)->not->toContain('Invisible Widget');
});

it('returns at most 6 suggestions', function () {
    $category = Category::factory()->create();
    $listingType = \App\Models\ListingType::factory()->create();

    Listing::factory()->count(10)->create([
        'title' => 'Vintage Camera',
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
        'category_id' => $category->id,
        'listing_type_id' => $listingType->id,
    ]);

    $data = $this->getJson('/search/suggestions?q=Vintage')->assertOk()->json();

    expect(count($data))->toBeLessThanOrEqual(6);
});

it('returns the listing url pointing to the show page', function () {
    $listing = Listing::factory()->create([
        'title' => 'Antique Lamp',
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
    ]);

    $data = $this->getJson('/search/suggestions?q=Antique')->assertOk()->json();

    expect($data[0]['url'])->toContain($listing->slug);
});

it('is accessible to guests without authentication', function () {
    $this->getJson('/search/suggestions?q=test')->assertOk();
});
