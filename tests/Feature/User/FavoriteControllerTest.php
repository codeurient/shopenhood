<?php

use App\Models\Favorite;
use App\Models\Listing;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

// ==========================================
// FAVORITES PAGE
// ==========================================

test('guest is redirected from favorites page', function () {
    $this->get(route('user.favorites.index'))
        ->assertRedirect(route('login'));
});

test('auth user can view favorites page', function () {
    $this->actingAs($this->user)
        ->get(route('user.favorites.index'))
        ->assertSuccessful()
        ->assertSee('My Favorites');
});

test('favorites page shows empty state when no favorites', function () {
    $this->actingAs($this->user)
        ->get(route('user.favorites.index'))
        ->assertSuccessful()
        ->assertSee('No favorites yet');
});

test('favorites page shows favorited listings', function () {
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);
    $this->user->favoriteListings()->attach($listing->id);

    $this->actingAs($this->user)
        ->get(route('user.favorites.index'))
        ->assertSuccessful()
        ->assertSee($listing->title);
});

test('favorites page does not show hidden listings', function () {
    $hidden = Listing::factory()->create([
        'status' => 'active',
        'is_visible' => true,
        'hidden_due_to_role_change' => true,
    ]);
    $this->user->favoriteListings()->attach($hidden->id);

    $this->actingAs($this->user)
        ->get(route('user.favorites.index'))
        ->assertSuccessful()
        ->assertDontSee($hidden->title);
});

test('favorites page does not show another user favorites', function () {
    $other = User::factory()->create();
    $listing = Listing::factory()->create(['status' => 'active', 'is_visible' => true]);
    $other->favoriteListings()->attach($listing->id);

    $this->actingAs($this->user)
        ->get(route('user.favorites.index'))
        ->assertSuccessful()
        ->assertDontSee($listing->title);
});

// ==========================================
// TOGGLE ENDPOINT
// ==========================================

test('guest is redirected when toggling favorite', function () {
    $listing = Listing::factory()->create();

    $this->postJson(route('api.favorites.toggle', $listing))
        ->assertUnauthorized();
});

test('auth user can favorite a listing', function () {
    $listing = Listing::factory()->create();

    $response = $this->actingAs($this->user)
        ->postJson(route('api.favorites.toggle', $listing));

    $response->assertSuccessful()
        ->assertJson(['favorited' => true]);

    expect(Favorite::where('user_id', $this->user->id)->where('listing_id', $listing->id)->exists())->toBeTrue();
});

test('auth user can unfavorite a listing', function () {
    $listing = Listing::factory()->create();
    $this->user->favoriteListings()->attach($listing->id);

    $response = $this->actingAs($this->user)
        ->postJson(route('api.favorites.toggle', $listing));

    $response->assertSuccessful()
        ->assertJson(['favorited' => false]);

    expect(Favorite::where('user_id', $this->user->id)->where('listing_id', $listing->id)->exists())->toBeFalse();
});

test('toggling favorite twice returns to original state', function () {
    $listing = Listing::factory()->create();

    $this->actingAs($this->user)->postJson(route('api.favorites.toggle', $listing));
    $this->actingAs($this->user)->postJson(route('api.favorites.toggle', $listing));

    expect(Favorite::where('user_id', $this->user->id)->where('listing_id', $listing->id)->exists())->toBeFalse();
});

test('favoriting does not duplicate rows for same listing', function () {
    $listing = Listing::factory()->create();

    $this->actingAs($this->user)->postJson(route('api.favorites.toggle', $listing));
    $this->actingAs($this->user)->postJson(route('api.favorites.toggle', $listing));
    $this->actingAs($this->user)->postJson(route('api.favorites.toggle', $listing));

    expect(Favorite::where('user_id', $this->user->id)->where('listing_id', $listing->id)->count())->toBe(1);
});

test('two users can favorite the same listing independently', function () {
    $other = User::factory()->create();
    $listing = Listing::factory()->create();

    $this->actingAs($this->user)->postJson(route('api.favorites.toggle', $listing));
    $this->actingAs($other)->postJson(route('api.favorites.toggle', $listing));

    expect(Favorite::where('listing_id', $listing->id)->count())->toBe(2);
});
