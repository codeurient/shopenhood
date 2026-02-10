<?php

use App\Models\Listing;
use App\Models\User;
use App\Notifications\ListingApprovedNotification;
use App\Notifications\ListingRejectedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->admin = User::factory()->create(['current_role' => 'admin']);
    $this->actingAs($this->admin, 'admin');
});

test('admin can approve a pending listing', function () {
    Notification::fake();

    $listing = Listing::factory()->create(['status' => 'pending']);

    $response = $this->post(route('admin.listings.approval.approve', $listing));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $listing->refresh();
    expect($listing->status)->toBe('active');
    expect($listing->approved_by)->toBe($this->admin->id);
    expect($listing->approved_at)->not->toBeNull();
    expect($listing->expires_at)->not->toBeNull();
    expect($listing->rejection_reason)->toBeNull();
    expect($listing->rejected_at)->toBeNull();
});

test('approving a listing sends notification to owner', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $listing = Listing::factory()->create([
        'status' => 'pending',
        'user_id' => $owner->id,
    ]);

    $this->post(route('admin.listings.approval.approve', $listing));

    Notification::assertSentTo($owner, ListingApprovedNotification::class);
});

test('admin cannot approve a non-pending listing', function () {
    $listing = Listing::factory()->create(['status' => 'active']);

    $response = $this->post(route('admin.listings.approval.approve', $listing));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $listing->refresh();
    expect($listing->status)->toBe('active');
});

test('admin can reject a pending listing with reason', function () {
    Notification::fake();

    $listing = Listing::factory()->create(['status' => 'pending']);

    $response = $this->post(route('admin.listings.approval.reject', $listing), [
        'rejection_reason' => 'This listing violates our guidelines.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $listing->refresh();
    expect($listing->status)->toBe('rejected');
    expect($listing->rejection_reason)->toBe('This listing violates our guidelines.');
    expect($listing->rejected_at)->not->toBeNull();
});

test('rejecting a listing sends notification to owner', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $listing = Listing::factory()->create([
        'status' => 'pending',
        'user_id' => $owner->id,
    ]);

    $this->post(route('admin.listings.approval.reject', $listing), [
        'rejection_reason' => 'Inappropriate content.',
    ]);

    Notification::assertSentTo($owner, ListingRejectedNotification::class);
});

test('rejection requires a reason', function () {
    $listing = Listing::factory()->create(['status' => 'pending']);

    $response = $this->post(route('admin.listings.approval.reject', $listing), [
        'rejection_reason' => '',
    ]);

    $response->assertSessionHasErrors('rejection_reason');
});

test('rejection reason must be 500 chars or less', function () {
    $listing = Listing::factory()->create(['status' => 'pending']);

    $response = $this->post(route('admin.listings.approval.reject', $listing), [
        'rejection_reason' => str_repeat('a', 501),
    ]);

    $response->assertSessionHasErrors('rejection_reason');
});

test('admin cannot reject a non-pending listing', function () {
    $listing = Listing::factory()->create(['status' => 'active']);

    $response = $this->post(route('admin.listings.approval.reject', $listing), [
        'rejection_reason' => 'Some reason.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $listing->refresh();
    expect($listing->status)->toBe('active');
});

test('approving a previously rejected listing clears rejection fields', function () {
    Notification::fake();

    $listing = Listing::factory()->create([
        'status' => 'pending',
        'rejection_reason' => 'Old reason',
        'rejected_at' => now()->subDay(),
    ]);

    $this->post(route('admin.listings.approval.approve', $listing));

    $listing->refresh();
    expect($listing->status)->toBe('active');
    expect($listing->rejection_reason)->toBeNull();
    expect($listing->rejected_at)->toBeNull();
});

// ==========================================
// ROLE CHANGE RESTRICTIONS
// ==========================================

test('approving listing after user downgrade hides listing if user already has active listing', function () {
    Notification::fake();

    // User was business_user, now is normal_user
    $user = User::factory()->create([
        'current_role' => 'normal_user',
        'is_business_enabled' => false,
    ]);

    // User already has one active visible listing
    Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'hidden_due_to_role_change' => false,
        'created_as_role' => 'business_user',
    ]);

    // Pending listing created when user was business_user
    $pendingListing = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'hidden_due_to_role_change' => false,
        'created_as_role' => 'business_user',
    ]);

    $response = $this->post(route('admin.listings.approval.approve', $pendingListing));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $pendingListing->refresh();
    expect($pendingListing->status)->toBe('active');
    expect($pendingListing->hidden_due_to_role_change)->toBeTrue();
});

test('approving listing after user downgrade does not hide if user has no active listings', function () {
    Notification::fake();

    // User was business_user, now is normal_user, but has no active listings
    $user = User::factory()->create([
        'current_role' => 'normal_user',
        'is_business_enabled' => false,
    ]);

    // Pending listing created when user was business_user
    $pendingListing = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'hidden_due_to_role_change' => false,
        'created_as_role' => 'business_user',
    ]);

    $response = $this->post(route('admin.listings.approval.approve', $pendingListing));

    $response->assertRedirect();

    $pendingListing->refresh();
    expect($pendingListing->status)->toBe('active');
    expect($pendingListing->hidden_due_to_role_change)->toBeFalse();
});

test('approving listing for business user does not apply role restrictions', function () {
    Notification::fake();

    // User is still a business_user
    $user = User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    // User already has active listings
    Listing::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => 'active',
        'hidden_due_to_role_change' => false,
        'created_as_role' => 'business_user',
    ]);

    // Pending listing
    $pendingListing = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'hidden_due_to_role_change' => false,
        'created_as_role' => 'business_user',
    ]);

    $response = $this->post(route('admin.listings.approval.approve', $pendingListing));

    $response->assertRedirect();

    $pendingListing->refresh();
    expect($pendingListing->status)->toBe('active');
    expect($pendingListing->hidden_due_to_role_change)->toBeFalse();
});

test('approving listing created as normal user does not apply role restrictions', function () {
    Notification::fake();

    // User is a normal_user
    $user = User::factory()->create([
        'current_role' => 'normal_user',
    ]);

    // Pending listing created as normal_user
    $pendingListing = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'hidden_due_to_role_change' => false,
        'created_as_role' => 'normal_user',
    ]);

    $response = $this->post(route('admin.listings.approval.approve', $pendingListing));

    $response->assertRedirect();

    $pendingListing->refresh();
    expect($pendingListing->status)->toBe('active');
    expect($pendingListing->hidden_due_to_role_change)->toBeFalse();
});

test('success message indicates role restriction when listing is hidden', function () {
    Notification::fake();

    $user = User::factory()->create([
        'current_role' => 'normal_user',
        'is_business_enabled' => false,
    ]);

    Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'active',
        'hidden_due_to_role_change' => false,
        'created_as_role' => 'business_user',
    ]);

    $pendingListing = Listing::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'created_as_role' => 'business_user',
    ]);

    $response = $this->post(route('admin.listings.approval.approve', $pendingListing));

    $response->assertSessionHas('success');
    expect(session('success'))->toContain('hidden due to user role restrictions');
});
