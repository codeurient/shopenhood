<?php

use App\Models\Listing;
use App\Models\Order;
use App\Models\User;

it('redirects guests to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('loads successfully for a verified user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertSuccessful();
});

it('shows the user name on the dashboard', function () {
    $user = User::factory()->create(['name' => 'Jane Seller']);

    $this->actingAs($user)->get(route('dashboard'))->assertSee('Jane Seller');
});

it('shows correct active listing count', function () {
    $user = User::factory()->create();
    Listing::factory()->count(3)->create(['user_id' => $user->id, 'status' => 'active']);
    Listing::factory()->count(1)->create(['user_id' => $user->id, 'status' => 'pending']);

    $this->actingAs($user)->get(route('dashboard'))->assertSee('3');
});

it('shows completed revenue from seller orders', function () {
    $user = User::factory()->create();
    $buyer = User::factory()->create();

    Order::factory()->create([
        'seller_id' => $user->id,
        'buyer_id' => $buyer->id,
        'status' => 'completed',
        'total_amount' => 150.00,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSee('150.00');
});

it('shows pending order count', function () {
    $user = User::factory()->create();
    $buyer = User::factory()->create();

    Order::factory()->count(2)->create([
        'seller_id' => $user->id,
        'buyer_id' => $buyer->id,
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSee('2');
});

it('shows notifications section', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSee('Notifications');
});

it('shows top selling listings section', function () {
    $user = User::factory()->create();
    $buyer = User::factory()->create();
    $listing = Listing::factory()->create(['user_id' => $user->id, 'status' => 'active']);

    Order::factory()->count(3)->create([
        'seller_id' => $user->id,
        'buyer_id' => $buyer->id,
        'listing_id' => $listing->id,
        'status' => 'completed',
        'total_amount' => 50.00,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSee('Top Selling Listings')
        ->assertSee($listing->title);
});

it('shows chart script on the page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSee('salesChart', false)
        ->assertSee('chart.js', false);
});
