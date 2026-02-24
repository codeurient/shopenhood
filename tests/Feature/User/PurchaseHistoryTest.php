<?php

use App\Models\Purchase;
use App\Models\User;

// ==========================================
// MY ORDERS (BUYER PURCHASE HISTORY)
// ==========================================

it('guest cannot view my-orders', function () {
    $this->get(route('user.purchases.index'))->assertRedirect(route('login'));
});

it('buyer can see their own purchases list', function () {
    $buyer = User::factory()->create();
    Purchase::factory()->forBuyer($buyer->id)->count(3)->create();

    $this->actingAs($buyer)
        ->get(route('user.purchases.index'))
        ->assertOk()
        ->assertViewIs('user.orders.index')
        ->assertViewHas('purchases');
});

it('buyer only sees their own purchases, not others', function () {
    $buyer = User::factory()->create();
    $otherBuyer = User::factory()->create();

    Purchase::factory()->forBuyer($buyer->id)->create(['purchase_number' => 'PUR-OWN-XXXXXX']);
    Purchase::factory()->forBuyer($otherBuyer->id)->create(['purchase_number' => 'PUR-OTHER-XXXXX']);

    $response = $this->actingAs($buyer)
        ->get(route('user.purchases.index'))
        ->assertOk();

    $purchases = $response->viewData('purchases');
    expect($purchases->pluck('buyer_id')->unique()->toArray())->toBe([$buyer->id]);
});

it('buyer can view their purchase detail', function () {
    $buyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($buyer->id)->create();

    $this->actingAs($buyer)
        ->get(route('user.purchases.show', $purchase))
        ->assertOk()
        ->assertViewIs('user.orders.show')
        ->assertViewHas('purchase');
});

it('buyer cannot view another users purchase', function () {
    $buyer = User::factory()->create();
    $otherBuyer = User::factory()->create();
    $purchase = Purchase::factory()->forBuyer($otherBuyer->id)->create();

    $this->actingAs($buyer)
        ->get(route('user.purchases.show', $purchase))
        ->assertForbidden();
});
