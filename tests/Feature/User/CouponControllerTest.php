<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponRestriction;
use App\Models\Listing;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['current_role' => 'business_user', 'is_business_enabled' => true]);
    $this->actingAs($this->user);
});

// ==========================================
// INDEX
// ==========================================

test('user can view their coupons index', function () {
    Coupon::factory()->forUser($this->user)->count(3)->create();

    $response = $this->get(route('user.coupons.index'));

    $response->assertSuccessful();
});

test('user only sees their own coupons', function () {
    $ownCoupon = Coupon::factory()->forUser($this->user)->create(['code' => 'MYCODE123']);
    $otherCoupon = Coupon::factory()->create(['code' => 'OTHERCODE', 'user_id' => User::factory()->create()->id]);

    $response = $this->get(route('user.coupons.index'));

    $response->assertSuccessful();
    $response->assertSee('MYCODE123');
    $response->assertDontSee('OTHERCODE');
});

// ==========================================
// CREATE
// ==========================================

test('user can view create coupon page', function () {
    $response = $this->get(route('user.coupons.create'));

    $response->assertSuccessful();
});

test('create page only shows categories from users own listings', function () {
    $usedCategory = Category::factory()->create(['name' => 'UserCategory']);
    $unusedCategory = Category::factory()->create(['name' => 'UnusedCategory']);

    Listing::factory()->create([
        'user_id' => $this->user->id,
        'category_id' => $usedCategory->id,
        'status' => 'active',
    ]);

    $response = $this->get(route('user.coupons.create'));

    $response->assertSuccessful();
    $response->assertSee('UserCategory');
    $response->assertDontSee('UnusedCategory');
});

test('create page only shows users own listings', function () {
    $ownListing = Listing::factory()->create([
        'user_id' => $this->user->id,
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
        'status' => 'active',
    ]);

    $data = [
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
    ];

    $this->post(route('user.coupons.store'), $data);

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
});

// ==========================================
// EDIT & UPDATE
// ==========================================

test('user can view edit page for their own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->user)->create();

    $response = $this->get(route('user.coupons.edit', $coupon));

    $response->assertSuccessful();
    $response->assertSee($coupon->code);
});

test('user cannot edit another users coupon', function () {
    $otherUser = User::factory()->create();
    $coupon = Coupon::factory()->forUser($otherUser)->create();

    $response = $this->get(route('user.coupons.edit', $coupon));

    $response->assertForbidden();
});

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

    $data = [
        'code' => 'NEWCODE',
        'type' => 'percentage',
        'value' => 20,
        'applicable_to' => 'all',
        'is_active' => true,
    ];

    $response = $this->put(route('user.coupons.update', $coupon), $data);

    $response->assertRedirect(route('user.coupons.index'));
    $response->assertSessionHas('success');

    $coupon->refresh();
    expect($coupon->code)->toBe('NEWCODE');
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
});

// ==========================================
// DELETE
// ==========================================

test('user can delete their own coupon', function () {
    $coupon = Coupon::factory()->forUser($this->user)->create();

    $response = $this->delete(route('user.coupons.destroy', $coupon));

    $response->assertRedirect(route('user.coupons.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
});

test('user cannot delete another users coupon', function () {
    $otherUser = User::factory()->create();
    $coupon = Coupon::factory()->forUser($otherUser)->create();

    $response = $this->delete(route('user.coupons.destroy', $coupon));

    $response->assertForbidden();
    $this->assertDatabaseHas('coupons', ['id' => $coupon->id]);
});

// ==========================================
// TOGGLE STATUS
// ==========================================

test('user can toggle their own coupon status', function () {
    $coupon = Coupon::factory()->forUser($this->user)->create(['is_active' => true]);

    $response = $this->patch(route('user.coupons.toggle-status', $coupon));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $coupon->refresh();
    expect($coupon->is_active)->toBeFalse();
});

test('user cannot toggle another users coupon status', function () {
    $otherUser = User::factory()->create();
    $coupon = Coupon::factory()->forUser($otherUser)->create(['is_active' => true]);

    $response = $this->patch(route('user.coupons.toggle-status', $coupon));

    $response->assertForbidden();

    $coupon->refresh();
    expect($coupon->is_active)->toBeTrue();
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
