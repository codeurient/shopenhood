<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Listing;
use App\Models\User;

beforeEach(function () {
    $this->businessUser = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $this->actingAs($this->businessUser);
});

// ==========================================
// ACCESS CONTROL
// ==========================================

test('normal user cannot access coupons', function () {
    $normalUser = User::factory()->create(['current_role' => 'normal_user']);
    $this->actingAs($normalUser);

    $response = $this->get(route('user.coupons.index'));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error');
});

test('business user can access coupons index', function () {
    $response = $this->get(route('user.coupons.index'));

    $response->assertSuccessful();
});

// ==========================================
// INDEX & FILTERING
// ==========================================

test('business user can view their own coupons', function () {
    Coupon::factory()->forUser($this->businessUser->id)->count(3)->create();

    $response = $this->get(route('user.coupons.index'));

    $response->assertSuccessful();
});

test('business user cannot see other users coupons', function () {
    $otherUser = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    Coupon::factory()->forUser($otherUser->id)->create(['code' => 'OTHERCPN']);
    Coupon::factory()->forUser($this->businessUser->id)->create(['code' => 'MYCPN']);

    $response = $this->get(route('user.coupons.index'));

    $response->assertSuccessful();
    $response->assertSee('MYCPN');
    $response->assertDontSee('OTHERCPN');
});

test('business user can search their coupons by code', function () {
    Coupon::factory()->forUser($this->businessUser->id)->create(['code' => 'SUMMER2026']);
    Coupon::factory()->forUser($this->businessUser->id)->create(['code' => 'WINTER2026']);

    $response = $this->get(route('user.coupons.index', ['search' => 'SUMMER']));

    $response->assertSuccessful();
    $response->assertSee('SUMMER2026');
    $response->assertDontSee('WINTER2026');
});

test('business user can filter coupons by type', function () {
    Coupon::factory()->forUser($this->businessUser->id)->create(['type' => 'percentage', 'code' => 'PERCENT10']);
    Coupon::factory()->forUser($this->businessUser->id)->create(['type' => 'fixed', 'code' => 'FIXED20']);

    $response = $this->get(route('user.coupons.index', ['type' => 'percentage']));

    $response->assertSuccessful();
    $response->assertSee('PERCENT10');
    $response->assertDontSee('FIXED20');
});

// ==========================================
// CREATE
// ==========================================

test('business user can view create coupon page', function () {
    $response = $this->get(route('user.coupons.create'));

    $response->assertSuccessful();
});

test('business user can create a basic coupon', function () {
    $data = [
        'code' => 'TESTCODE',
        'type' => 'percentage',
        'value' => 25,
        'applicable_to' => 'all',
        'is_active' => true,
    ];

    $response = $this->post(route('user.coupons.store'), $data);

    $response->assertRedirect(route('user.coupons.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('coupons', [
        'code' => 'TESTCODE',
        'type' => 'percentage',
        'value' => 25,
        'user_id' => $this->businessUser->id,
    ]);
});

test('created coupon belongs to current user', function () {
    $data = [
        'code' => 'MYCODE',
        'type' => 'fixed',
        'value' => 10,
        'applicable_to' => 'all',
    ];

    $this->post(route('user.coupons.store'), $data);

    $coupon = Coupon::where('code', 'MYCODE')->first();
    expect($coupon->user_id)->toBe($this->businessUser->id);
});

// ==========================================
// CATEGORY RESTRICTIONS
// ==========================================

test('create form only shows categories from user listings', function () {
    $usedCategory = Category::factory()->create(['name' => 'Used Category']);
    $unusedCategory = Category::factory()->create(['name' => 'Unused Category']);

    Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'category_id' => $usedCategory->id,
        'status' => 'active',
    ]);

    $response = $this->get(route('user.coupons.create'));

    $response->assertSuccessful();
    $response->assertSee('Used Category');
    $response->assertDontSee('Unused Category');
});

test('coupon can only be restricted to user owned categories', function () {
    $usedCategory = Category::factory()->create();
    $otherCategory = Category::factory()->create();

    Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'category_id' => $usedCategory->id,
        'status' => 'active',
    ]);

    $data = [
        'code' => 'CATTEST',
        'type' => 'percentage',
        'value' => 10,
        'applicable_to' => 'categories',
        'restrictions' => [$usedCategory->id, $otherCategory->id],
    ];

    $this->post(route('user.coupons.store'), $data);

    $coupon = Coupon::where('code', 'CATTEST')->first();
    expect($coupon->restrictions)->toHaveCount(1);
    expect($coupon->restrictions->first()->restrictable_id)->toBe($usedCategory->id);
});

// ==========================================
// LISTING RESTRICTIONS
// ==========================================

test('create form only shows user own listings', function () {
    $myListing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'title' => 'My Product',
        'status' => 'active',
    ]);
    $otherListing = Listing::factory()->create([
        'title' => 'Other Product',
        'status' => 'active',
    ]);

    $response = $this->get(route('user.coupons.create'));

    $response->assertSuccessful();
    $response->assertSee('My Product');
    $response->assertDontSee('Other Product');
});

test('coupon can only be restricted to user owned listings', function () {
    $myListing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'status' => 'active',
    ]);
    $otherListing = Listing::factory()->create([
        'status' => 'active',
    ]);

    $data = [
        'code' => 'LISTTEST',
        'type' => 'fixed',
        'value' => 5,
        'applicable_to' => 'listings',
        'restrictions' => [$myListing->id, $otherListing->id],
    ];

    $this->post(route('user.coupons.store'), $data);

    $coupon = Coupon::where('code', 'LISTTEST')->first();
    expect($coupon->restrictions)->toHaveCount(1);
    expect($coupon->restrictions->first()->restrictable_id)->toBe($myListing->id);
});

// ==========================================
// EDIT & UPDATE
// ==========================================

test('business user can view edit page for own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->businessUser->id)->create();

    $response = $this->get(route('user.coupons.edit', $coupon));

    $response->assertSuccessful();
    $response->assertSee($coupon->code);
});

test('business user cannot edit other users coupon', function () {
    $otherUser = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $coupon = Coupon::factory()->forUser($otherUser->id)->create();

    $response = $this->get(route('user.coupons.edit', $coupon));

    $response->assertForbidden();
});

test('business user can update own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->businessUser->id)->create(['code' => 'OLDCODE', 'value' => 10]);

    $data = [
        'code' => 'NEWCODE',
        'type' => 'percentage',
        'value' => 30,
        'applicable_to' => 'all',
        'is_active' => true,
    ];

    $response = $this->put(route('user.coupons.update', $coupon), $data);

    $response->assertRedirect(route('user.coupons.index'));
    $response->assertSessionHas('success');

    $coupon->refresh();
    expect($coupon->code)->toBe('NEWCODE');
    expect((float) $coupon->value)->toBe(30.00);
});

// ==========================================
// DELETE
// ==========================================

test('business user can delete own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->businessUser->id)->create();

    $response = $this->delete(route('user.coupons.destroy', $coupon));

    $response->assertRedirect(route('user.coupons.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
});

test('business user cannot delete other users coupon', function () {
    $otherUser = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $coupon = Coupon::factory()->forUser($otherUser->id)->create();

    $response = $this->delete(route('user.coupons.destroy', $coupon));

    $response->assertForbidden();
    $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
});

// ==========================================
// TOGGLE STATUS
// ==========================================

test('business user can toggle own coupon status', function () {
    $coupon = Coupon::factory()->forUser($this->businessUser->id)->create(['is_active' => true]);

    $response = $this->patch(route('user.coupons.toggle-status', $coupon));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $coupon->refresh();
    expect($coupon->is_active)->toBeFalse();
});

test('business user cannot toggle other users coupon status', function () {
    $otherUser = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $coupon = Coupon::factory()->forUser($otherUser->id)->create(['is_active' => true]);

    $response = $this->patch(route('user.coupons.toggle-status', $coupon));

    $response->assertForbidden();
});

// ==========================================
// VALIDATION
// ==========================================

test('coupon code must be unique', function () {
    Coupon::factory()->create(['code' => 'DUPLICATE']);

    $data = [
        'code' => 'DUPLICATE',
        'type' => 'percentage',
        'value' => 10,
        'applicable_to' => 'all',
    ];

    $response = $this->post(route('user.coupons.store'), $data);

    $response->assertSessionHasErrors('code');
});

test('validation rejects missing required fields', function () {
    $response = $this->post(route('user.coupons.store'), []);

    $response->assertSessionHasErrors(['code', 'type', 'value', 'applicable_to']);
});
