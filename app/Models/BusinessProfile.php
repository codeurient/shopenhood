<?php

namespace App\Models;

use App\Services\SensitiveDataEncryptionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessProfile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Sensitive encrypted columns — never exposed via toArray() / JSON.
     *
     * These columns store AES-256-GCM ciphertext. Plaintext is only available
     * through decryptSensitiveData(), which enforces admin authorisation.
     */
    protected $hidden = [
        'registration_number',
        'tax_id',
        'fin',
        'id_number',
        'id_full_name',
    ];

    protected $fillable = [
        'user_id',
        'business_name',
        'legal_name',
        'slug',
        'description',
        'registration_number',
        'tax_id',
        'fin',
        'id_number',
        'id_full_name',
        'industry',
        'business_type',
        'address_line_1',
        'address_line_2',
        'city',
        'state_province',
        'postal_code',
        'country_id',
        'business_email',
        'business_phone',
        'website',
        'logo',
        'banner',
        'default_currency',
        'timezone',
        'return_policy',
        'shipping_policy',
        'approved_at',
        'confident_seller_status',
        'confident_seller_rejection_reason',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'user_id' => 'integer',
        'approved_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'country_id');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state_province,
            $this->postal_code,
            $this->country?->name,
        ])->filter()->implode(', ');
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Decrypt and return all sensitive identity/tax fields.
     *
     * Only callable by an admin user. Throws AuthorizationException otherwise,
     * ensuring that no non-admin code path can access plaintext values.
     *
     * @return array{registration_number: ?string, tax_id: ?string, fin: ?string, id_number: ?string, id_full_name: ?string}
     *
     * @throws AuthorizationException
     */
    public function decryptSensitiveData(User $admin): array
    {
        if (! $admin->isAdmin()) {
            throw new AuthorizationException('Only administrators may access sensitive identity data.');
        }

        $encryption = app(SensitiveDataEncryptionService::class);

        return [
            'registration_number' => $encryption->decryptNullable($this->registration_number),
            'tax_id' => $encryption->decryptNullable($this->tax_id),
            'fin' => $encryption->decryptNullable($this->fin),
            'id_number' => $encryption->decryptNullable($this->id_number),
            'id_full_name' => $encryption->decryptNullable($this->id_full_name),
        ];
    }

    /**
     * Decrypt and return all sensitive fields for the profile owner's own view.
     *
     * Only the user who owns this profile may call this method.
     *
     * @return array{registration_number: ?string, tax_id: ?string, fin: ?string, id_number: ?string, id_full_name: ?string}
     *
     * @throws AuthorizationException
     */
    public function decryptForOwner(User $owner): array
    {
        if ($owner->id !== $this->user_id) {
            throw new AuthorizationException('You may only view your own sensitive profile data.');
        }

        $encryption = app(SensitiveDataEncryptionService::class);

        return [
            'registration_number' => $encryption->decryptNullable($this->registration_number),
            'tax_id' => $encryption->decryptNullable($this->tax_id),
            'fin' => $encryption->decryptNullable($this->fin),
            'id_number' => $encryption->decryptNullable($this->id_number),
            'id_full_name' => $encryption->decryptNullable($this->id_full_name),
        ];
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    public function isConfidentSellerPending(): bool
    {
        return $this->confident_seller_status === 'pending';
    }

    public function isConfidentSellerApproved(): bool
    {
        return $this->confident_seller_status === 'approved';
    }

    public function isConfidentSellerRejected(): bool
    {
        return $this->confident_seller_status === 'rejected';
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopeByIndustry($query, string $industry)
    {
        return $query->where('industry', $industry);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('business_name', 'like', "%{$term}%")
                ->orWhere('legal_name', 'like', "%{$term}%")
                ->orWhere('business_email', 'like', "%{$term}%");
        });
    }
}
