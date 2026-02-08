<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'is_default',
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
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street,
            $this->building ? 'Building '.$this->building : null,
            $this->apartment ? 'Apt '.$this->apartment : null,
        ]);

        $addressLine = implode(', ', $parts);

        $locationParts = array_filter([
            $this->district,
            $this->city,
            $this->country,
        ]);

        $locationLine = implode(', ', $locationParts);

        $postalLine = $this->postal_code ? 'Postal Code: '.$this->postal_code : null;

        return implode("\n", array_filter([$addressLine, $locationLine, $postalLine]));
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->country,
            $this->city,
            $this->district,
            $this->street,
            $this->building ? 'Building '.$this->building : null,
            $this->apartment ? 'Apt '.$this->apartment : null,
        ]);

        return implode(', ', $parts);
    }

    public function getRecipientInfoAttribute(): string
    {
        return $this->recipient_name.' ('.$this->phone.')';
    }

    public function toOrderSnapshot(): array
    {
        return [
            'label' => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'country' => $this->country,
            'city' => $this->city,
            'district' => $this->district,
            'street' => $this->street,
            'building' => $this->building,
            'apartment' => $this->apartment,
            'postal_code' => $this->postal_code,
            'additional_notes' => $this->additional_notes,
            'full_address' => $this->full_address,
        ];
    }
}
