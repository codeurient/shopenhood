<?php

use App\Services\SensitiveDataEncryptionService;

// ==========================================
// ENCRYPT / DECRYPT ROUNDTRIP
// ==========================================

test('encrypts and decrypts a string correctly', function () {
    $service = app(SensitiveDataEncryptionService::class);

    $plaintext = 'AZ1234567';

    $encrypted = $service->encrypt($plaintext);
    expect($encrypted)->not->toBe($plaintext);

    $decrypted = $service->decrypt($encrypted);
    expect($decrypted)->toBe($plaintext);
});

test('each encryption call produces a different ciphertext (random nonce)', function () {
    $service = app(SensitiveDataEncryptionService::class);

    $plaintext = 'same-value';

    $first = $service->encrypt($plaintext);
    $second = $service->encrypt($plaintext);

    expect($first)->not->toBe($second);

    // But both decrypt to the same plaintext
    expect($service->decrypt($first))->toBe($plaintext);
    expect($service->decrypt($second))->toBe($plaintext);
});

test('decryption fails if ciphertext is tampered with', function () {
    $service = app(SensitiveDataEncryptionService::class);

    $encrypted = $service->encrypt('sensitive-data');
    $tampered = substr($encrypted, 0, -4).'XXXX';

    expect(fn () => $service->decrypt($tampered))->toThrow(RuntimeException::class);
});

test('decryption fails for a random string', function () {
    $service = app(SensitiveDataEncryptionService::class);

    expect(fn () => $service->decrypt(base64_encode('not-a-valid-blob')))->toThrow(RuntimeException::class);
});

// ==========================================
// NULLABLE HELPERS
// ==========================================

test('encryptNullable returns null for null input', function () {
    $service = app(SensitiveDataEncryptionService::class);

    expect($service->encryptNullable(null))->toBeNull();
});

test('encryptNullable returns null for empty string', function () {
    $service = app(SensitiveDataEncryptionService::class);

    expect($service->encryptNullable(''))->toBeNull();
});

test('encryptNullable encrypts non-empty strings', function () {
    $service = app(SensitiveDataEncryptionService::class);

    $result = $service->encryptNullable('FIN123456');
    expect($result)->not->toBeNull();
    expect($service->decrypt($result))->toBe('FIN123456');
});

test('decryptNullable returns null for null input', function () {
    $service = app(SensitiveDataEncryptionService::class);

    expect($service->decryptNullable(null))->toBeNull();
});

// ==========================================
// BUSINESS PROFILE: SENSITIVE FIELD STORAGE
// ==========================================

test('business user submitting profile stores sensitive fields encrypted', function () {
    $user = \App\Models\User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $listingType = \App\Models\ListingType::first();

    $this->actingAs($user)
        ->post(route('business.profile.store'), [
            'business_name' => 'Test Business',
            'fin' => 'FIN9876543',
            'id_number' => 'ID12345678',
            'id_full_name' => 'John Sample Doe',
        ])
        ->assertRedirect(route('business.profile'));

    $profile = $user->fresh()->businessProfile;

    // Raw DB values must not be plaintext
    expect($profile->fin)->not->toBe('FIN9876543');
    expect($profile->id_number)->not->toBe('ID12345678');
    expect($profile->id_full_name)->not->toBe('John Sample Doe');

    // The encrypted blob must be a valid base64 string
    expect(base64_decode($profile->fin, true))->not->toBeFalse();
});

test('sensitive fields are excluded from model toArray()', function () {
    $profile = \App\Models\BusinessProfile::factory()->create([
        'fin' => 'raw-encrypted-blob',
        'id_number' => 'raw-encrypted-blob',
    ]);

    $array = $profile->toArray();

    expect($array)->not->toHaveKey('fin');
    expect($array)->not->toHaveKey('id_number');
    expect($array)->not->toHaveKey('id_full_name');
    expect($array)->not->toHaveKey('registration_number');
    expect($array)->not->toHaveKey('tax_id');
});

// ==========================================
// ADMIN DECRYPT ACCESS
// ==========================================

test('admin can decrypt sensitive data via decryptSensitiveData()', function () {
    $service = app(SensitiveDataEncryptionService::class);
    $admin = \App\Models\User::factory()->create(['current_role' => 'admin']);

    $profile = \App\Models\BusinessProfile::factory()->create([
        'fin' => $service->encrypt('FIN9876543'),
        'id_number' => $service->encrypt('ID12345678'),
        'id_full_name' => $service->encrypt('John Sample Doe'),
    ]);

    $data = $profile->decryptSensitiveData($admin);

    expect($data['fin'])->toBe('FIN9876543');
    expect($data['id_number'])->toBe('ID12345678');
    expect($data['id_full_name'])->toBe('John Sample Doe');
});

test('non-admin cannot call decryptSensitiveData()', function () {
    $normalUser = \App\Models\User::factory()->create(['current_role' => 'normal_user']);
    $profile = \App\Models\BusinessProfile::factory()->create();

    expect(fn () => $profile->decryptSensitiveData($normalUser))
        ->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);
});

test('business user cannot call decryptSensitiveData()', function () {
    $businessUser = \App\Models\User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $profile = \App\Models\BusinessProfile::factory()->create(['user_id' => $businessUser->id]);

    expect(fn () => $profile->decryptSensitiveData($businessUser))
        ->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);
});

test('decryptSensitiveData returns nulls for unset sensitive fields', function () {
    $admin = \App\Models\User::factory()->create(['current_role' => 'admin']);
    $profile = \App\Models\BusinessProfile::factory()->create([
        'fin' => null,
        'id_number' => null,
        'id_full_name' => null,
    ]);

    $data = $profile->decryptSensitiveData($admin);

    expect($data['fin'])->toBeNull();
    expect($data['id_number'])->toBeNull();
    expect($data['id_full_name'])->toBeNull();
});

// ==========================================
// OWNER SELF-VIEW: decryptForOwner()
// ==========================================

test('profile owner can decrypt their own sensitive data via decryptForOwner()', function () {
    $service = app(SensitiveDataEncryptionService::class);
    $owner = \App\Models\User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    $profile = \App\Models\BusinessProfile::factory()->create([
        'user_id' => $owner->id,
        'fin' => $service->encrypt('FIN1234567'),
        'id_number' => $service->encrypt('ID9876543'),
        'id_full_name' => $service->encrypt('Jane Owner Doe'),
    ]);

    $data = $profile->decryptForOwner($owner);

    expect($data['fin'])->toBe('FIN1234567');
    expect($data['id_number'])->toBe('ID9876543');
    expect($data['id_full_name'])->toBe('Jane Owner Doe');
});

test('different user cannot call decryptForOwner() on another profile', function () {
    $owner = \App\Models\User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $other = \App\Models\User::factory()->create(['current_role' => 'normal_user']);
    $profile = \App\Models\BusinessProfile::factory()->create(['user_id' => $owner->id]);

    expect(fn () => $profile->decryptForOwner($other))
        ->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);
});

test('decryptForOwner returns nulls for unset sensitive fields', function () {
    $owner = \App\Models\User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);
    $profile = \App\Models\BusinessProfile::factory()->create([
        'user_id' => $owner->id,
        'fin' => null,
        'id_number' => null,
        'id_full_name' => null,
    ]);

    $data = $profile->decryptForOwner($owner);

    expect($data['fin'])->toBeNull();
    expect($data['id_number'])->toBeNull();
    expect($data['id_full_name'])->toBeNull();
});

test('business profile show page displays decrypted sensitive data to owner', function () {
    $service = app(SensitiveDataEncryptionService::class);
    $owner = \App\Models\User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    \App\Models\BusinessProfile::factory()->create([
        'user_id' => $owner->id,
        'fin' => $service->encrypt('FIN-OWNER-TEST'),
        'id_number' => $service->encrypt('ID-OWNER-99'),
        'id_full_name' => $service->encrypt('Owner Full Name'),
    ]);

    $this->actingAs($owner)
        ->get(route('business.profile'))
        ->assertSuccessful()
        ->assertSee('FIN-OWNER-TEST')
        ->assertSee('ID-OWNER-99')
        ->assertSee('Owner Full Name');
});

test('business profile edit page displays sensitive data as read-only to owner', function () {
    $owner = \App\Models\User::factory()->create([
        'current_role' => 'business_user',
        'is_business_enabled' => true,
    ]);

    \App\Models\BusinessProfile::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner)
        ->get(route('business.profile.edit'))
        ->assertSuccessful()
        ->assertSeeText('Identity & Tax Information', false)
        ->assertSee('Read-only');
});
