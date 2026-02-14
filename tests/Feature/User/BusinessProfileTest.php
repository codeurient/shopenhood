<?php

use App\Models\BusinessProfile;
use App\Models\User;

test('business user can view own profile', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $profile = BusinessProfile::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->get(route('business.profile'));

    $response->assertSuccessful();
    $response->assertSee($profile->business_name);
});

test('normal user cannot access business profile', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);

    $this->actingAs($user);

    $response = $this->get(route('business.profile'));

    $response->assertRedirect(route('dashboard'));
});

test('business user without profile is redirected to create page', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('business.profile'));

    $response->assertRedirect(route('business.profile.create'));
    $response->assertSessionHas('info');
});

test('business user can view create profile page', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('business.profile.create'));

    $response->assertSuccessful();
    $response->assertSee('Set Up Your Business Profile');
});

test('business user can create own profile', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $this->actingAs($user);

    $response = $this->post(route('business.profile.store'), [
        'business_name' => 'My New Business',
        'description' => 'A great business',
        'business_email' => 'contact@mybusiness.com',
    ]);

    $response->assertRedirect(route('business.profile'));
    $response->assertSessionHas('success');

    expect($user->businessProfile)->not->toBeNull();
    expect($user->businessProfile->business_name)->toBe('My New Business');
});

test('business user with existing profile cannot access create page', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    BusinessProfile::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->get(route('business.profile.create'));

    $response->assertRedirect(route('business.profile'));
});

test('business user can view edit profile page', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $profile = BusinessProfile::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->get(route('business.profile.edit'));

    $response->assertSuccessful();
    $response->assertSee($profile->business_name);
});

test('business user can update own profile', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $profile = BusinessProfile::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->put(route('business.profile.update'), [
        'description' => 'Updated description',
        'business_email' => 'newemail@business.com',
        'business_phone' => '+1234567890',
        'website' => 'https://new-website.com',
        'address_line_1' => '123 New Street',
        'city' => 'New City',
        'return_policy' => 'Updated return policy',
    ]);

    $response->assertRedirect(route('business.profile'));
    $response->assertSessionHas('success');

    $profile->refresh();
    expect($profile->description)->toBe('Updated description');
    expect($profile->business_email)->toBe('newemail@business.com');
    expect($profile->city)->toBe('New City');
});

test('normal user cannot update business profile', function () {
    $user = User::factory()->create(['current_role' => 'normal_user']);

    $this->actingAs($user);

    $response = $this->put(route('business.profile.update'), [
        'description' => 'Trying to update',
    ]);

    $response->assertForbidden();
});

test('business user profile update validates email format', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    BusinessProfile::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->put(route('business.profile.update'), [
        'business_email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['business_email']);
});

test('business user profile update validates website format', function () {
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    BusinessProfile::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->put(route('business.profile.update'), [
        'website' => 'not-a-valid-url',
    ]);

    $response->assertSessionHasErrors(['website']);
});

test('guest cannot access business profile', function () {
    $response = $this->get(route('business.profile'));

    $response->assertRedirect(route('login'));
});

test('guest cannot update business profile', function () {
    $response = $this->put(route('business.profile.update'), [
        'description' => 'Trying to update',
    ]);

    $response->assertRedirect(route('login'));
});
