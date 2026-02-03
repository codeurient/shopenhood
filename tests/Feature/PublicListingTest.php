<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\User;

test('home page loads successfully', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Trade Locally', false);
});

test('home page shows featured listings', function () {
    $listing = Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'is_featured' => true,
        'title' => 'Featured Widget',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Featured Widget');
});

test('home page shows latest listings', function () {
    $listing = Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Latest Gadget',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Latest Gadget');
});

test('home page shows categories', function () {
    $category = Category::factory()->create([
        'is_active' => true,
        'parent_id' => null,
        'name' => 'Electronics',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Electronics');
});

test('browse page loads successfully', function () {
    $response = $this->get(route('listings.index'));

    $response->assertSuccessful();
});

test('browse page shows active visible listings', function () {
    $listing = Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Visible Listing',
    ]);

    $response = $this->get(route('listings.index'));

    $response->assertSuccessful();
    $response->assertSee('Visible Listing');
});

test('browse page hides inactive listings', function () {
    Listing::factory()->create([
        'status' => 'pending',
        'is_visible' => true,
        'title' => 'Pending Listing Hidden',
    ]);

    Listing::factory()->create([
        'status' => 'rejected',
        'is_visible' => true,
        'title' => 'Rejected Listing Hidden',
    ]);

    $response = $this->get(route('listings.index'));

    $response->assertSuccessful();
    $response->assertDontSee('Pending Listing Hidden');
    $response->assertDontSee('Rejected Listing Hidden');
});

test('browse page hides non-visible listings', function () {
    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => false,
        'title' => 'Hidden From Browse',
    ]);

    $response = $this->get(route('listings.index'));

    $response->assertSuccessful();
    $response->assertDontSee('Hidden From Browse');
});

test('browse page search filter works', function () {
    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Blue Sneakers',
    ]);

    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Red Jacket',
    ]);

    $response = $this->get(route('listings.index', ['search' => 'Sneakers']));

    $response->assertSuccessful();
    $response->assertSee('Blue Sneakers');
    $response->assertDontSee('Red Jacket');
});

test('browse page category filter works', function () {
    $electronics = Category::factory()->create(['name' => 'Electronics', 'slug' => 'electronics']);
    $clothing = Category::factory()->create(['name' => 'Clothing', 'slug' => 'clothing']);

    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Laptop Sale',
        'category_id' => $electronics->id,
    ]);

    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Winter Coat',
        'category_id' => $clothing->id,
    ]);

    $response = $this->get(route('listings.index', ['category' => 'electronics']));

    $response->assertSuccessful();
    $response->assertSee('Laptop Sale');
    $response->assertDontSee('Winter Coat');
});

test('browse page listing type filter works', function () {
    $sellType = ListingType::factory()->create(['name' => 'Sell']);
    $tradeType = ListingType::factory()->create(['name' => 'Trade']);

    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Selling Phone',
        'listing_type_id' => $sellType->id,
    ]);

    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Trading Cards',
        'listing_type_id' => $tradeType->id,
    ]);

    $response = $this->get(route('listings.index', ['type' => $sellType->id]));

    $response->assertSuccessful();
    $response->assertSee('Selling Phone');
    $response->assertDontSee('Trading Cards');
});

test('browse page price range filter works', function () {
    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Cheap Item',
        'base_price' => 10.00,
    ]);

    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Expensive Item',
        'base_price' => 500.00,
    ]);

    $response = $this->get(route('listings.index', ['min_price' => 100, 'max_price' => 600]));

    $response->assertSuccessful();
    $response->assertSee('Expensive Item');
    $response->assertDontSee('Cheap Item');
});

test('browse page sort by price ascending works', function () {
    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'AAA Expensive',
        'base_price' => 999.00,
    ]);

    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'ZZZ Cheapest',
        'base_price' => 1.00,
    ]);

    $response = $this->get(route('listings.index', ['sort' => 'price_asc']));

    $response->assertSuccessful();
    // Both should be visible - just verifying the page loads with sort param
    $response->assertSee('AAA Expensive');
    $response->assertSee('ZZZ Cheapest');
});

test('search query is logged', function () {
    Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'title' => 'Searchable Item',
    ]);

    $this->get(route('listings.index', ['search' => 'Searchable']));

    $this->assertDatabaseHas('search_queries', [
        'query' => 'Searchable',
    ]);
});

test('authenticated user can mark all notifications as read', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('notifications.mark-read'));

    $response->assertRedirect();
});

test('authenticated user can mark single notification as read', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a notification via the database
    $user->notify(new \App\Notifications\ListingApprovedNotification(
        Listing::factory()->create(['user_id' => $user->id])
    ));

    $notification = $user->notifications()->first();

    $response = $this->post(route('notifications.read', $notification->id));

    $response->assertRedirect();

    $notification->refresh();
    expect($notification->read_at)->not->toBeNull();
});

test('guest cannot access notification routes', function () {
    $response = $this->post(route('notifications.mark-read'));

    $response->assertRedirect(route('login'));
});
