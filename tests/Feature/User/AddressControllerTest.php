<?php

use App\Models\User;
use App\Models\UserAddress;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// ==========================================
// ACCESS CONTROL
// ==========================================

test('guest cannot access addresses', function () {
    auth()->logout();

    $response = $this->get(route('user.addresses.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated user can access addresses index', function () {
    $response = $this->get(route('user.addresses.index'));

    $response->assertSuccessful();
});

// ==========================================
// INDEX
// ==========================================

test('user can view their own addresses', function () {
    UserAddress::factory()->forUser($this->user->id)->count(3)->create();

    $response = $this->get(route('user.addresses.index'));

    $response->assertSuccessful();
});

test('user cannot see other users addresses', function () {
    $otherUser = User::factory()->create();
    UserAddress::factory()->forUser($otherUser->id)->create(['recipient_name' => 'Other Person']);
    UserAddress::factory()->forUser($this->user->id)->create(['recipient_name' => 'My Recipient']);

    $response = $this->get(route('user.addresses.index'));

    $response->assertSuccessful();
    $response->assertSee('My Recipient');
    $response->assertDontSee('Other Person');
});

test('addresses are sorted with default first', function () {
    UserAddress::factory()->forUser($this->user->id)->create(['recipient_name' => 'Not Default', 'is_default' => false]);
    UserAddress::factory()->forUser($this->user->id)->default()->create(['recipient_name' => 'Default Address']);

    $response = $this->get(route('user.addresses.index'));

    $response->assertSuccessful();
    $response->assertSeeInOrder(['Default', 'Default Address', 'Not Default']);
});

// ==========================================
// CREATE
// ==========================================

test('user can view create address page', function () {
    $response = $this->get(route('user.addresses.create'));

    $response->assertSuccessful();
});

test('user can create an address', function () {
    $data = [
        'label' => 'Home',
        'recipient_name' => 'John Doe',
        'phone' => '+1234567890',
        'email' => 'john@example.com',
        'country' => 'United States',
        'city' => 'New York',
        'district' => 'Manhattan',
        'street' => '123 Main Street',
        'building' => 'Tower A',
        'apartment' => '5B',
        'postal_code' => '10001',
        'additional_notes' => 'Ring doorbell twice',
    ];

    $response = $this->post(route('user.addresses.store'), $data);

    $response->assertRedirect(route('user.addresses.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('user_addresses', [
        'user_id' => $this->user->id,
        'label' => 'Home',
        'recipient_name' => 'John Doe',
        'city' => 'New York',
    ]);
});

test('first address is automatically set as default', function () {
    $data = [
        'label' => 'Home',
        'recipient_name' => 'John Doe',
        'phone' => '+1234567890',
        'country' => 'United States',
        'city' => 'New York',
        'street' => '123 Main Street',
    ];

    $this->post(route('user.addresses.store'), $data);

    $address = UserAddress::where('user_id', $this->user->id)->first();
    expect($address->is_default)->toBeTrue();
});

test('second address is not automatically set as default', function () {
    UserAddress::factory()->forUser($this->user->id)->default()->create();

    $data = [
        'label' => 'Work',
        'recipient_name' => 'John Doe',
        'phone' => '+1234567890',
        'country' => 'United States',
        'city' => 'New York',
        'street' => '456 Office Ave',
    ];

    $this->post(route('user.addresses.store'), $data);

    $newAddress = UserAddress::where('street', '456 Office Ave')->first();
    expect($newAddress->is_default)->toBeFalse();
});

test('setting new address as default unsets previous default', function () {
    $firstAddress = UserAddress::factory()->forUser($this->user->id)->default()->create();

    $data = [
        'label' => 'Work',
        'recipient_name' => 'John Doe',
        'phone' => '+1234567890',
        'country' => 'United States',
        'city' => 'New York',
        'street' => '456 Office Ave',
        'is_default' => true,
    ];

    $this->post(route('user.addresses.store'), $data);

    $firstAddress->refresh();
    expect($firstAddress->is_default)->toBeFalse();

    $newAddress = UserAddress::where('street', '456 Office Ave')->first();
    expect($newAddress->is_default)->toBeTrue();
});

// ==========================================
// EDIT & UPDATE
// ==========================================

test('user can view edit page for own address', function () {
    $address = UserAddress::factory()->forUser($this->user->id)->create();

    $response = $this->get(route('user.addresses.edit', $address));

    $response->assertSuccessful();
    $response->assertSee($address->recipient_name);
});

test('user cannot edit other users address', function () {
    $otherUser = User::factory()->create();
    $address = UserAddress::factory()->forUser($otherUser->id)->create();

    $response = $this->get(route('user.addresses.edit', $address));

    $response->assertForbidden();
});

test('user can update own address', function () {
    $address = UserAddress::factory()->forUser($this->user->id)->create([
        'recipient_name' => 'Old Name',
        'city' => 'Old City',
    ]);

    $data = [
        'label' => 'Work',
        'recipient_name' => 'New Name',
        'phone' => '+9876543210',
        'country' => 'Canada',
        'city' => 'Toronto',
        'street' => '789 New Street',
    ];

    $response = $this->put(route('user.addresses.update', $address), $data);

    $response->assertRedirect(route('user.addresses.index'));
    $response->assertSessionHas('success');

    $address->refresh();
    expect($address->recipient_name)->toBe('New Name');
    expect($address->city)->toBe('Toronto');
});

test('user cannot update other users address', function () {
    $otherUser = User::factory()->create();
    $address = UserAddress::factory()->forUser($otherUser->id)->create();

    $data = [
        'label' => 'Work',
        'recipient_name' => 'Hacker',
        'phone' => '+1234567890',
        'country' => 'Hacked',
        'city' => 'Hacked',
        'street' => 'Hacked',
    ];

    $response = $this->put(route('user.addresses.update', $address), $data);

    $response->assertForbidden();
});

// ==========================================
// DELETE
// ==========================================

test('user can delete own address', function () {
    $address = UserAddress::factory()->forUser($this->user->id)->create();

    $response = $this->delete(route('user.addresses.destroy', $address));

    $response->assertRedirect(route('user.addresses.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('user_addresses', ['id' => $address->id]);
});

test('user cannot delete other users address', function () {
    $otherUser = User::factory()->create();
    $address = UserAddress::factory()->forUser($otherUser->id)->create();

    $response = $this->delete(route('user.addresses.destroy', $address));

    $response->assertForbidden();
    $this->assertDatabaseHas('user_addresses', ['id' => $address->id]);
});

test('deleting default address promotes another to default', function () {
    $defaultAddress = UserAddress::factory()->forUser($this->user->id)->default()->create();
    $otherAddress = UserAddress::factory()->forUser($this->user->id)->create(['is_default' => false]);

    $this->delete(route('user.addresses.destroy', $defaultAddress));

    $otherAddress->refresh();
    expect($otherAddress->is_default)->toBeTrue();
});

// ==========================================
// SET DEFAULT
// ==========================================

test('user can set an address as default', function () {
    $oldDefault = UserAddress::factory()->forUser($this->user->id)->default()->create();
    $newDefault = UserAddress::factory()->forUser($this->user->id)->create(['is_default' => false]);

    $response = $this->patch(route('user.addresses.set-default', $newDefault));

    $response->assertRedirect(route('user.addresses.index'));
    $response->assertSessionHas('success');

    $oldDefault->refresh();
    $newDefault->refresh();

    expect($oldDefault->is_default)->toBeFalse();
    expect($newDefault->is_default)->toBeTrue();
});

test('user cannot set other users address as default', function () {
    $otherUser = User::factory()->create();
    $address = UserAddress::factory()->forUser($otherUser->id)->create();

    $response = $this->patch(route('user.addresses.set-default', $address));

    $response->assertForbidden();
});

// ==========================================
// API ENDPOINTS
// ==========================================

test('user can get their addresses via api', function () {
    UserAddress::factory()->forUser($this->user->id)->count(2)->create();

    $response = $this->getJson(route('api.user.addresses'));

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'addresses' => [
            '*' => [
                'id',
                'label',
                'is_default',
                'recipient_name',
                'phone',
                'full_address',
                'formatted_address',
            ],
        ],
    ]);
    expect($response->json('addresses'))->toHaveCount(2);
});

test('api only returns current user addresses', function () {
    $otherUser = User::factory()->create();
    UserAddress::factory()->forUser($otherUser->id)->create();
    UserAddress::factory()->forUser($this->user->id)->create();

    $response = $this->getJson(route('api.user.addresses'));

    $response->assertSuccessful();
    expect($response->json('addresses'))->toHaveCount(1);
});

test('user can get single address via api', function () {
    $address = UserAddress::factory()->forUser($this->user->id)->create();

    $response = $this->getJson(route('api.user.address', $address));

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'address' => [
            'id',
            'label',
            'recipient_name',
            'phone',
            'email',
            'country',
            'city',
            'district',
            'street',
            'building',
            'apartment',
            'postal_code',
            'additional_notes',
            'full_address',
        ],
    ]);
});

test('user cannot get other users address via api', function () {
    $otherUser = User::factory()->create();
    $address = UserAddress::factory()->forUser($otherUser->id)->create();

    $response = $this->getJson(route('api.user.address', $address));

    $response->assertForbidden();
});

// ==========================================
// VALIDATION
// ==========================================

test('validation rejects missing required fields', function () {
    $response = $this->post(route('user.addresses.store'), []);

    $response->assertSessionHasErrors(['label', 'recipient_name', 'phone', 'country', 'city', 'street']);
});

test('email must be valid format', function () {
    $data = [
        'label' => 'Home',
        'recipient_name' => 'John Doe',
        'phone' => '+1234567890',
        'email' => 'invalid-email',
        'country' => 'United States',
        'city' => 'New York',
        'street' => '123 Main Street',
    ];

    $response = $this->post(route('user.addresses.store'), $data);

    $response->assertSessionHasErrors('email');
});

test('label must not exceed max length', function () {
    $data = [
        'label' => str_repeat('a', 51),
        'recipient_name' => 'John Doe',
        'phone' => '+1234567890',
        'country' => 'United States',
        'city' => 'New York',
        'street' => '123 Main Street',
    ];

    $response = $this->post(route('user.addresses.store'), $data);

    $response->assertSessionHasErrors('label');
});

// ==========================================
// MODEL METHODS
// ==========================================

test('address returns formatted address', function () {
    $address = UserAddress::factory()->forUser($this->user->id)->create([
        'street' => '123 Main Street',
        'building' => 'Tower A',
        'apartment' => '5B',
        'district' => 'Manhattan',
        'city' => 'New York',
        'country' => 'USA',
        'postal_code' => '10001',
    ]);

    expect($address->formatted_address)->toContain('123 Main Street');
    expect($address->formatted_address)->toContain('New York');
    expect($address->formatted_address)->toContain('USA');
});

test('address toOrderSnapshot returns complete data', function () {
    $address = UserAddress::factory()->forUser($this->user->id)->create([
        'label' => 'Home',
        'recipient_name' => 'John Doe',
        'phone' => '+1234567890',
        'city' => 'New York',
        'country' => 'USA',
        'street' => '123 Main Street',
    ]);

    $snapshot = $address->toOrderSnapshot();

    expect($snapshot)->toHaveKeys([
        'label',
        'recipient_name',
        'phone',
        'email',
        'country',
        'city',
        'district',
        'street',
        'building',
        'apartment',
        'postal_code',
        'additional_notes',
        'full_address',
    ]);
    expect($snapshot['recipient_name'])->toBe('John Doe');
    expect($snapshot['city'])->toBe('New York');
});

test('belongsToUser returns correct boolean', function () {
    $address = UserAddress::factory()->forUser($this->user->id)->create();

    expect($address->belongsToUser($this->user->id))->toBeTrue();
    expect($address->belongsToUser($this->user->id + 1))->toBeFalse();
});
