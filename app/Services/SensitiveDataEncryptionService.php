<?php

namespace App\Services;

use Illuminate\Auth\Access\AuthorizationException;
use RuntimeException;

/**
 * AES-256-GCM encryption service for sensitive personal and tax identity data.
 *
 * Storage format: base64( nonce[12] + tag[16] + ciphertext )
 *
 * GCM provides authenticated encryption: any tampering with the ciphertext will
 * cause decryption to fail, preventing padding-oracle and bit-flipping attacks.
 *
 * The key is sourced from SENSITIVE_DATA_KEY in .env — intentionally separate
 * from APP_KEY so that compromising APP_KEY alone cannot expose this data.
 */
class SensitiveDataEncryptionService
{
    private readonly string $key;

    public function __construct()
    {
        $encoded = config('sensitive.key');

        if (! $encoded) {
            throw new RuntimeException(
                'SENSITIVE_DATA_KEY is not configured. Generate one with: '.
                'php artisan tinker --execute="echo base64_encode(random_bytes(32));"'
            );
        }

        $key = base64_decode($encoded, strict: true);

        if ($key === false || strlen($key) !== 32) {
            throw new RuntimeException(
                'SENSITIVE_DATA_KEY must be a base64-encoded 32-byte (256-bit) key.'
            );
        }

        $this->key = $key;
    }

    /**
     * Encrypt a plaintext string using AES-256-GCM.
     *
     * A fresh 12-byte nonce is generated for every call, ensuring that
     * encrypting the same value twice produces different ciphertexts.
     */
    public function encrypt(string $plaintext): string
    {
        $nonce = random_bytes(12);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            '',
            16
        );

        if ($ciphertext === false) {
            throw new RuntimeException('AES-256-GCM encryption failed.');
        }

        return base64_encode($nonce.$tag.$ciphertext);
    }

    /**
     * Decrypt a value produced by encrypt().
     *
     * @throws RuntimeException if the blob is malformed or the GCM tag fails
     *                          (indicating tampering or wrong key).
     */
    public function decrypt(string $encrypted): string
    {
        $decoded = base64_decode($encrypted, strict: true);

        if ($decoded === false || strlen($decoded) < 29) {
            throw new RuntimeException('Encrypted blob is malformed.');
        }

        $nonce = substr($decoded, 0, 12);
        $tag = substr($decoded, 12, 16);
        $ciphertext = substr($decoded, 28);

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );

        if ($plaintext === false) {
            throw new RuntimeException(
                'Decryption failed: authentication tag mismatch. '.
                'The data may have been tampered with or the key is incorrect.'
            );
        }

        return $plaintext;
    }

    /**
     * Encrypt a nullable value — null and empty strings pass through as null.
     */
    public function encryptNullable(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->encrypt($value);
    }

    /**
     * Decrypt a nullable value — null passes through as null.
     *
     * @throws AuthorizationException always — call only from authorized admin code paths.
     */
    public function decryptNullable(?string $encrypted): ?string
    {
        if ($encrypted === null) {
            return null;
        }

        return $this->decrypt($encrypted);
    }
}
