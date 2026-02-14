<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'legal_name',
        'slug',
        'description',
        'registration_number',
        'tax_id',
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
    ];

    protected $casts = [
        'country_id' => 'integer',
        'user_id' => 'integer',
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
    // SCOPES
    // ============================================

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
