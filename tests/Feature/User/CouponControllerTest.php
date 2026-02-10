<?php

use App\Models\Category;
use App\Models\Coupon;
<<<<<<< HEAD
=======
use App\Models\CouponRestriction;
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
use App\Models\Listing;
use App\Models\User;

beforeEach(function () {
<<<<<<< HEAD
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
=======
    $this->user = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $this->actingAs($this->user);
});

// ==========================================
// INDEX
// ==========================================

test('user can view their coupons index', function () {
    Coupon::factory()->forUser($this->user)->count(3)->create();
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $response = $this->get(route('user.coupons.index'));

    $response->assertSuccessful();
});

<<<<<<< HEAD
test('business user cannot see other users coupons', function () {
    $otherUser = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    Coupon::factory()->forUser($otherUser->id)->create(['code' => 'OTHERCPN']);
    Coupon::factory()->forUser($this->businessUser->id)->create(['code' => 'MYCPN']);
=======
test('user only sees their own coupons', function () {
    $ownCoupon = Coupon::factory()->forUser($this->user)->create(['code' => 'MYCODE123']);
    $otherCoupon = Coupon::factory()->create(['code' => 'OTHERCODE', 'user_id' => User::factory()->create()->id]);
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $response = $this->get(route('user.coupons.index'));

    $response->assertSuccessful();
<<<<<<< HEAD
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
=======
    $response->assertSee('MYCODE123');
    $response->assertDontSee('OTHERCODE');
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
});

// ==========================================
// CREATE
// ==========================================

<<<<<<< HEAD
test('business user can view create coupon page', function () {
=======
test('user can view create coupon page', function () {
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
    $response = $this->get(route('user.coupons.create'));

    $response->assertSuccessful();
});

<<<<<<< HEAD
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
=======
test('create page only shows categories from users own listings', function () {
    $usedCategory = Category::factory()->create(['name' => 'UserCategory']);
    $unusedCategory = Category::factory()->create(['name' => 'UnusedCategory']);

    Listing::factory()->create([
        'user_id' => $this->user->id,
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
        'category_id' => $usedCategory->id,
        'status' => 'active',
    ]);

    $response = $this->get(route('user.coupons.create'));

    $response->assertSuccessful();
<<<<<<< HEAD
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
=======
    $response->assertSee('UserCategory');
    $response->assertDontSee('UnusedCategory');
});

test('create page only shows users own listings', function () {
    $ownListing = Listing::factory()->create([
        'user_id' => $this->user->id,
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
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

<<<<<<< HEAD
test('coupon can only be restricted to user owned listings', function () {
    $myListing = Listing::factory()->create([
        'user_id' => $this->businessUser->id,
        'status' => 'active',
    ]);
    $otherListing = Listing::factory()->create([
=======
test('user can create a basic coupon', function () {
    $data = [
        'code' => 'MYSHOP10',
        'type' => 'percentage',
        'value' => 10,
        'applicable_to' => 'all',
        'is_active' => true,
    ];

    $response = $this->post(route('user.coupons.store'), $data);

    $response->assertRedirect(route('user.coupons.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('coupons', [
        'code' => 'MYSHOP10',
        'user_id' => $this->user->id,
        'type' => 'percentage',
        'applicable_to' => 'all',
    ]);
});

test('user can create a coupon with category restrictions', function () {
    $category = Category::factory()->create();

    Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $category->id,
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
        'status' => 'active',
    ]);

    $data = [
<<<<<<< HEAD
        'code' => 'LISTTEST',
        'type' => 'fixed',
        'value' => 5,
        'applicable_to' => 'listings',
        'restrictions' => [$myListing->id, $otherListing->id],
=======
        'code' => 'CATDEAL',
        'type' => 'fixed',
        'value' => 5,
        'applicable_to' => 'categories',
        'restrictions' => [$category->id],
        'is_active' => true,
    ];

    $response = $this->post(route('user.coupons.store'), $data);

    $response->assertRedirect(route('user.coupons.index'));

    $coupon = Coupon::where('code', 'CATDEAL')->first();
    expect($coupon)->not->toBeNull();
    expect($coupon->restrictions)->toHaveCount(1);
    expect($coupon->restrictions->first()->restrictable_type)->toBe(Category::class);
});

test('user can create a coupon with listing restrictions', function () {
    $listing = Listing::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'active',
    ]);

    $data = [
        'code' => 'LISTDEAL',
        'type' => 'percentage',
        'value' => 15,
        'applicable_to' => 'listings',
        'restrictions' => [$listing->id],
        'is_active' => true,
    ];

    $response = $this->post(route('user.coupons.store'), $data);

    $response->assertRedirect(route('user.coupons.index'));

    $coupon = Coupon::where('code', 'LISTDEAL')->first();
    expect($coupon)->not->toBeNull();
    expect($coupon->restrictions)->toHaveCount(1);
    expect($coupon->restrictions->first()->restrictable_type)->toBe(Listing::class);
});

test('coupon code is stored as uppercase', function () {
    $data = [
        'code' => 'lowercase',
        'type' => 'percentage',
        'value' => 10,
        'applicable_to' => 'all',
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
    ];

    $this->post(route('user.coupons.store'), $data);

<<<<<<< HEAD
    $coupon = Coupon::where('code', 'LISTTEST')->first();
    expect($coupon->restrictions)->toHaveCount(1);
    expect($coupon->restrictions->first()->restrictable_id)->toBe($myListing->id);
=======
    $this->assertDatabaseHas('coupons', ['code' => 'LOWERCASE']);
});

test('user cannot set applicable_to as users', function () {
    $data = [
        'code' => 'USERTARGET',
        'type' => 'percentage',
        'value' => 10,
        'applicable_to' => 'users',
    ];

    $response = $this->post(route('user.coupons.store'), $data);

    $response->assertSessionHasErrors('applicable_to');
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
});

// ==========================================
// EDIT & UPDATE
// ==========================================

<<<<<<< HEAD
test('business user can view edit page for own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->businessUser->id)->create();
=======
test('user can view edit page for their own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->user)->create();
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $response = $this->get(route('user.coupons.edit', $coupon));

    $response->assertSuccessful();
    $response->assertSee($coupon->code);
});

<<<<<<< HEAD
test('business user cannot edit other users coupon', function () {
    $otherUser = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $coupon = Coupon::factory()->forUser($otherUser->id)->create();
=======
test('user cannot edit another users coupon', function () {
    $otherUser = User::factory()->create();
    $coupon = Coupon::factory()->forUser($otherUser)->create();
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $response = $this->get(route('user.coupons.edit', $coupon));

    $response->assertForbidden();
});

<<<<<<< HEAD
test('business user can update own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->businessUser->id)->create(['code' => 'OLDCODE', 'value' => 10]);
=======
test('edit page only shows users own categories', function () {
    $usedCategory = Category::factory()->create(['name' => 'MyEditCategory']);
    $unusedCategory = Category::factory()->create(['name' => 'NotMyEditCategory']);

    Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $usedCategory->id,
        'status' => 'active',
    ]);

    $coupon = Coupon::factory()->forUser($this->user)->forCategories()->create();

    $response = $this->get(route('user.coupons.edit', $coupon));

    $response->assertSuccessful();
    $response->assertSee('MyEditCategory');
    $response->assertDontSee('NotMyEditCategory');
});

test('user can update their own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->user)->create(['code' => 'OLDCODE', 'value' => 10]);
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $data = [
        'code' => 'NEWCODE',
        'type' => 'percentage',
<<<<<<< HEAD
        'value' => 30,
=======
        'value' => 20,
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
        'applicable_to' => 'all',
        'is_active' => true,
    ];

    $response = $this->put(route('user.coupons.update', $coupon), $data);

    $response->assertRedirect(route('user.coupons.index'));
    $response->assertSessionHas('success');

    $coupon->refresh();
    expect($coupon->code)->toBe('NEWCODE');
<<<<<<< HEAD
    expect((float) $coupon->value)->toBe(30.00);
=======
    expect((float) $coupon->value)->toBe(20.00);
});

test('user cannot update another users coupon', function () {
    $otherUser = User::factory()->create();
    $coupon = Coupon::factory()->forUser($otherUser)->create();

    $data = [
        'code' => 'HIJACKED',
        'type' => 'percentage',
        'value' => 99,
        'applicable_to' => 'all',
    ];

    $response = $this->put(route('user.coupons.update', $coupon), $data);

    $response->assertForbidden();
});

test('updating coupon syncs restrictions', function () {
    $coupon = Coupon::factory()->forUser($this->user)->forCategories()->create();
    $oldCategory = Category::factory()->create();
    $newCategory = Category::factory()->create();

    Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $newCategory->id,
        'status' => 'active',
    ]);

    CouponRestriction::create([
        'coupon_id' => $coupon->id,
        'restrictable_type' => Category::class,
        'restrictable_id' => $oldCategory->id,
    ]);

    $data = [
        'code' => $coupon->code,
        'type' => $coupon->type,
        'value' => $coupon->value,
        'applicable_to' => 'categories',
        'restrictions' => [$newCategory->id],
    ];

    $this->put(route('user.coupons.update', $coupon), $data);

    $coupon->refresh();
    expect($coupon->restrictions)->toHaveCount(1);
    expect($coupon->restrictions->first()->restrictable_id)->toBe($newCategory->id);
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
});

// ==========================================
// DELETE
// ==========================================

<<<<<<< HEAD
test('business user can delete own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->businessUser->id)->create();
=======
test('user can delete their own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->user)->create();
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $response = $this->delete(route('user.coupons.destroy', $coupon));

    $response->assertRedirect(route('user.coupons.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
});

<<<<<<< HEAD
test('business user cannot delete other users coupon', function () {
    $otherUser = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $coupon = Coupon::factory()->forUser($otherUser->id)->create();
=======
test('user cannot delete another users coupon', function () {
    $otherUser = User::factory()->create();
    $coupon = Coupon::factory()->forUser($otherUser)->create();
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $response = $this->delete(route('user.coupons.destroy', $coupon));

    $response->assertForbidden();
    $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
});

// ==========================================
// TOGGLE STATUS
// ==========================================

<<<<<<< HEAD
test('business user can toggle own coupon status', function () {
    $coupon = Coupon::factory()->forUser($this->businessUser->id)->create(['is_active' => true]);
=======
test('user can toggle their own coupon status', function () {
    $coupon = Coupon::factory()->forUser($this->user)->create(['is_active' => true]);
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $response = $this->patch(route('user.coupons.toggle-status', $coupon));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $coupon->refresh();
    expect($coupon->is_active)->toBeFalse();
});

<<<<<<< HEAD
test('business user cannot toggle other users coupon status', function () {
    $otherUser = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $coupon = Coupon::factory()->forUser($otherUser->id)->create(['is_active' => true]);
=======
test('user cannot toggle another users coupon status', function () {
    $otherUser = User::factory()->create();
    $coupon = Coupon::factory()->forUser($otherUser)->create(['is_active' => true]);
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

    $response = $this->patch(route('user.coupons.toggle-status', $coupon));

    $response->assertForbidden();
<<<<<<< HEAD
=======

    $coupon->refresh();
    expect($coupon->is_active)->toBeTrue();
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
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
<<<<<<< HEAD
=======

test('expires_at must be after starts_at', function () {
    $data = [
        'code' => 'DATETEST',
        'type' => 'percentage',
        'value' => 10,
        'applicable_to' => 'all',
        'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        'expires_at' => now()->subDay()->format('Y-m-d H:i:s'),
    ];

    $response = $this->post(route('user.coupons.store'), $data);

    $response->assertSessionHasErrors('expires_at');
});

// ==========================================
// AUTHENTICATION
// ==========================================

test('guest cannot access coupon routes', function () {
    auth()->logout();

    $this->get(route('user.coupons.index'))->assertRedirect(route('login'));
    $this->get(route('user.coupons.create'))->assertRedirect(route('login'));
});
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
