<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, LogsActivity, Notifiable, SoftDeletes;

    /**
     * Mass assignable attributes.
     * SECURITY: Sensitive fields (current_role, is_business_enabled, listing_limit,
     * business_valid_until, status) are protected via $guarded to prevent privilege escalation.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'phone_verified_at',
        'daily_listing_count',
        'last_listing_date',
        'avatar',
        'bio',
    ];

    /**
     * Attributes protected from mass assignment.
     * SECURITY: These fields can only be set explicitly by admin controllers.
     */
    protected $guarded = [
        'id',
        'current_role',
        'is_business_enabled',
        'listing_limit',
        'business_valid_until',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'business_valid_until' => 'datetime',
        'last_listing_date' => 'date',
        'password' => 'hashed',
        'is_business_enabled' => 'boolean',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function listings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function favoriteListings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'favorites');
    }

    public function coupons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function loginHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function lastLogin(): ?\Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LoginHistory::class)->latestOfMany('logged_in_at');
    }

    public function businessProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BusinessProfile::class);
    }

    // ============================================
    // BUSINESS PROFILE HELPERS
    // ============================================

    public function hasBusinessProfile(): bool
    {
        return $this->businessProfile()->exists();
    }

    public function getBusinessNameAttribute(): ?string
    {
        return $this->businessProfile?->business_name;
    }

    // ============================================
    // ROLE HELPERS
    // ============================================

    public function isAdmin(): bool
    {
        return $this->current_role === 'admin';
    }

    public function isNormalUser(): bool
    {
        return $this->current_role === 'normal_user';
    }

    /**
     * A business user must have the role, be enabled, and not expired.
     */
    public function isBusinessUser(): bool
    {
        return $this->current_role === 'business_user'
            && $this->is_business_enabled
            && ($this->business_valid_until === null || $this->business_valid_until->isFuture());
    }

    /**
     * Get effective listing limit for this user.
     * Returns null for unlimited (business default), or integer limit.
     */
    public function getListingLimit(): ?int
    {
        if ($this->isBusinessUser()) {
            return $this->listing_limit; // null = unlimited
        }

        // Normal users always limited to 1
        return 1;
    }

    // ============================================
    // ACTIVITY LOG
    // ============================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'current_role', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
