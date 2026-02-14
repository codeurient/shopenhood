<?php

use App\Models\BusinessProfile;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can view business profiles index', function () {
    BusinessProfile::factory()->count(3)->create();

    $response = $this->get(route('admin.business-profiles.index'));

    $response->assertSuccessful();
});

test('admin can search business profiles', function () {
    BusinessProfile::factory()->create(['business_name' => 'Acme Corporation']);
    BusinessProfile::factory()->create(['business_name' => 'Beta Industries']);

    $response = $this->get(route('admin.business-profiles.index', ['search' => 'Acme']));

    $response->assertSuccessful();
    $response->assertSee('Acme Corporation');
});

test('admin can filter business profiles by industry', function () {
    BusinessProfile::factory()->create(['industry' => 'retail']);
    BusinessProfile::factory()->create(['industry' => 'wholesale']);

    $response = $this->get(route('admin.business-profiles.index', ['industry' => 'retail']));

    $response->assertSuccessful();
});

test('admin can view business profile show page', function () {
    $profile = BusinessProfile::factory()->create();

    $response = $this->get(route('admin.business-profiles.show', $profile));

    $response->assertSuccessful();
    $response->assertSee($profile->business_name);
});

test('admin can view create business profile page for business user', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $response = $this->get(route('admin.business-profiles.create', $user));

    $response->assertSuccessful();
});

test('admin cannot create business profile for non-business user', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);

    $response = $this->get(route('admin.business-profiles.create', $user));

    $response->assertRedirect(route('admin.users.edit', $user));
    $response->assertSessionHas('error');
});

test('admin cannot create duplicate business profile', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    BusinessProfile::factory()->create(['user_id' => $user->id]);

    $response = $this->get(route('admin.business-profiles.create', $user));

    $response->assertRedirect();
    $response->assertSessionHas('info');
});

test('admin can store new business profile', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $response = $this->post(route('admin.business-profiles.store', $user), [
        'business_name' => 'Test Business',
        'legal_name' => 'Test Business LLC',
        'description' => 'A test business description',
        'industry' => 'retail',
        'business_type' => 'llc',
        'business_email' => 'test@business.com',
        'default_currency' => 'USD',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($user->businessProfile)->not->toBeNull();
    expect($user->businessProfile->business_name)->toBe('Test Business');
});

test('admin can view edit business profile page', function () {
    $profile = BusinessProfile::factory()->create();

    $response = $this->get(route('admin.business-profiles.edit', $profile));

    $response->assertSuccessful();
    $response->assertSee($profile->business_name);
});

test('admin can update business profile', function () {
    $profile = BusinessProfile::factory()->create();

    $response = $this->put(route('admin.business-profiles.update', $profile), [
        'business_name' => 'Updated Business Name',
        'legal_name' => $profile->legal_name,
        'description' => 'Updated description',
        'default_currency' => 'EUR',
    ]);

    $response->assertRedirect(route('admin.business-profiles.show', $profile));
    $response->assertSessionHas('success');

    $profile->refresh();
    expect($profile->business_name)->toBe('Updated Business Name');
    expect($profile->default_currency)->toBe('EUR');
});

test('admin can delete business profile', function () {
    $profile = BusinessProfile::factory()->create();

    $response = $this->delete(route('admin.business-profiles.destroy', $profile));

    $response->assertRedirect(route('admin.business-profiles.index'));
    $response->assertSessionHas('success');

    expect(BusinessProfile::find($profile->id))->toBeNull();
    expect(BusinessProfile::withTrashed()->find($profile->id))->not->toBeNull();
});

test('business profile validation requires business name', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $response = $this->post(route('admin.business-profiles.store', $user), [
        'description' => 'A test business description',
    ]);

    $response->assertSessionHasErrors(['business_name']);
});

test('business profile validation rejects invalid email', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $response = $this->post(route('admin.business-profiles.store', $user), [
        'business_name' => 'Test Business',
        'business_email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['business_email']);
});

test('business profile validation rejects invalid website', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $response = $this->post(route('admin.business-profiles.store', $user), [
        'business_name' => 'Test Business',
        'website' => 'not-a-url',
    ]);

    $response->assertSessionHasErrors(['website']);
});
