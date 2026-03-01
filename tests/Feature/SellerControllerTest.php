<?php

use App\Models\Listing;
use App\Models\User;

it('shows a public seller profile page', function () {
    $seller = User::factory()->create(['status' => 'active']);

    $this->get(route('sellers.show', $seller))->assertOk();
});

it('is accessible to guests without authentication', function () {
    $seller = User::factory()->create(['status' => 'active']);

    $this->get(route('sellers.show', $seller))->assertOk();
});

it('returns 404 for a suspended user', function () {
    $seller = User::factory()->create(['status' => 'suspended']);

    $this->get(route('sellers.show', $seller))->assertNotFound();
});

it('returns 404 for a banned user', function () {
    $seller = User::factory()->create(['status' => 'banned']);

    $this->get(route('sellers.show', $seller))->assertNotFound();
});

it('returns 404 for an admin user', function () {
    $admin = User::factory()->create(['status' => 'active', 'current_role' => 'admin']);

    $this->get(route('sellers.show', $admin))->assertNotFound();
});

it('shows active listings for the seller', function () {
    $seller = User::factory()->create(['status' => 'active']);

    Listing::factory()->create([
        'user_id' => $seller->id,
        'title' => 'Visible Widget',
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
    ]);

    $this->get(route('sellers.show', $seller))
        ->assertOk()
        ->assertSee('Visible Widget');
});

it('does not show draft or hidden listings', function () {
    $seller = User::factory()->create(['status' => 'active']);

    Listing::factory()->create([
        'user_id' => $seller->id,
        'title' => 'Draft Item',
        'status' => 'draft',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
    ]);

    Listing::factory()->create([
        'user_id' => $seller->id,
        'title' => 'Hidden Item',
        'status' => 'active',
        'is_visible' => false,
        'hidden_due_to_role_change' => false,
    ]);

    $this->get(route('sellers.show', $seller))
        ->assertOk()
        ->assertDontSee('Draft Item')
        ->assertDontSee('Hidden Item');
});

it('shows the seller name on the profile page', function () {
    $seller = User::factory()->create(['status' => 'active', 'name' => 'Jane Doe']);

    $this->get(route('sellers.show', $seller))
        ->assertOk()
        ->assertSee('Jane Doe');
});

it('does not show listings from other sellers', function () {
    $seller = User::factory()->create(['status' => 'active']);
    $otherSeller = User::factory()->create(['status' => 'active']);

    Listing::factory()->create([
        'user_id' => $otherSeller->id,
        'title' => 'Other Sellers Item',
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => false,
    ]);

    $this->get(route('sellers.show', $seller))
        ->assertOk()
        ->assertDontSee('Other Sellers Item');
});
