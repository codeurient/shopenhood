<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Listing extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'listing_type_id',
        'title',
        'slug',
        'description',
        'short_description',
        'base_price',
        'discount_price',
        'discount_start_date',
        'discount_end_date',
        'currency',
        'status',
        'is_visible',
        'hidden_due_to_subscription',
        'hidden_due_to_role_change',
        'is_featured',
        'is_negotiable',
        'condition',
        'is_wholesale',
        'wholesale_min_order_qty',
        'wholesale_qty_increment',
        'wholesale_lead_time_days',
        'wholesale_sample_available',
        'wholesale_sample_price',
        'wholesale_terms',
        'availability_type',
        'has_delivery',
        'has_domestic_delivery',
        'domestic_delivery_price',
        'has_international_delivery',
        'international_delivery_price',
        'location_id',
        'country',
        'city',
        'created_as_role',
        'store_name',
        'meta_title',
        'meta_description',
        'expires_at',
        'approved_by',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'variant_attributes',
        'listing_mode',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_visible' => 'boolean',
        'hidden_due_to_subscription' => 'boolean',
        'hidden_due_to_role_change' => 'boolean',
        'is_featured' => 'boolean',
        'is_negotiable' => 'boolean',
        'is_wholesale' => 'boolean',
        'wholesale_min_order_qty' => 'integer',
        'wholesale_qty_increment' => 'integer',
        'wholesale_lead_time_days' => 'integer',
        'wholesale_sample_available' => 'boolean',
        'wholesale_sample_price' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'expires_at' => 'datetime',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
        'has_delivery' => 'boolean',
        'has_domestic_delivery' => 'boolean',
        'domestic_delivery_price' => 'decimal:2',
        'has_international_delivery' => 'boolean',
        'international_delivery_price' => 'decimal:2',
        'variant_attributes' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Owner of the listing
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Category this listing belongs to
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Listing type (sell, buy, gift, barter, auction)
    public function listingType()
    {
        return $this->belongsTo(ListingType::class);
    }

    // Location (if provided)
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Admin who approved this listing
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // All images for this listing
    public function images()
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    // Primary/featured image
    public function primaryImage()
    {
        return $this->hasOne(ListingImage::class)
            ->where('is_primary', true);
    }

    // First image (fallback if no primary)
    public function firstImage()
    {
        return $this->hasOne(ListingImage::class)
            ->oldestOfMany('sort_order');
    }

    // Selected variant values for this listing
    public function listingVariants()
    {
        return $this->hasMany(ListingVariant::class);
    }

    // Variants through listing_variants
    public function variants()
    {
        return $this->hasManyThrough(
            Variant::class,
            ListingVariant::class,
            'listing_id',       // Foreign key on listing_variants
            'id',               // Foreign key on variants
            'id',               // Local key on listings
            'variant_id'        // Local key on listing_variants
        );
    }

    // Product variations (SKU-level: size + color combinations)
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    // Available variations (active and in stock)
    public function availableVariations()
    {
        return $this->hasMany(ProductVariation::class)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('manage_stock', false)
                    ->orWhere('stock_quantity', '>', 0)
                    ->orWhere('allow_backorder', true);
            })
            ->orderBy('sort_order');
    }

    // Default variation for this listing
    public function defaultVariation()
    {
        return $this->hasOne(ProductVariation::class)->where('is_default', true);
    }

    // Orders for this listing
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Order items for this listing
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Reviews for this listing
    public function reviews()
    {
        return $this->hasMany(ListingReview::class)->latest();
    }

    public function averageRating(): float
    {
        return (float) $this->reviews()->avg('rating');
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
    }

    // Users who favorited this listing
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    // Favorites count relationship
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Conversations about this listing
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    // Shipping methods available for this listing
    public function shippingMethods()
    {
        return $this->belongsToMany(ShippingMethod::class, 'listing_shipping')
            ->withPivot('is_enabled', 'additional_cost')
            ->withTimestamps();
    }

    // Direct access to listing_shipping pivot records
    public function listingShipping()
    {
        return $this->hasMany(ListingShipping::class);
    }

    // Reports filed against this listing (polymorphic)
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    // Page views for this listing (polymorphic)
    public function pageViews()
    {
        return $this->morphMany(PageView::class, 'viewable');
    }

    // Activity logs for this listing (polymorphic - Spatie package)
    public function activities()
    {
        return $this->morphMany(
            \Spatie\Activitylog\Models\Activity::class,
            'subject'
        );
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->where('status', 'active');
    }

    public function scopePubliclyVisible($query)
    {
        return $query->where('status', 'active')
            ->where('is_visible', true)
            ->where('hidden_due_to_role_change', false);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%');
        });
    }

    public function scopeWholesale($query)
    {
        return $query->where('is_wholesale', true);
    }

    public function scopeNormalMode($query)
    {
        return $query->where('listing_mode', 'normal');
    }

    public function scopeBusinessMode($query)
    {
        return $query->where('listing_mode', 'business');
    }

    // ============================================
    // HELPERS
    // ============================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isReshareable(): bool
    {
        return $this->trashed();
    }

    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public function isWholesale(): bool
    {
        return (bool) $this->is_wholesale;
    }

    /**
     * Get country code from country name
     */
    public function getCountryCodeAttribute(): ?string
    {
        if (! $this->country) {
            return null;
        }

        // Try to find the country code from the locations table
        $location = Location::where('type', 'country')
            ->where('name', $this->country)
            ->first();

        if ($location && $location->code) {
            return $location->code;
        }

        // Fallback: manual mapping for common countries
        $countryCodeMap = [
            'United States' => 'US',
            'Azerbaijan' => 'AZ',
            'United Kingdom' => 'GB',
            'Germany' => 'DE',
            'France' => 'FR',
            'Turkey' => 'TR',
            'Russia' => 'RU',
            'China' => 'CN',
            'Japan' => 'JP',
            'India' => 'IN',
        ];

        return $countryCodeMap[$this->country] ?? substr($this->country, 0, 2);
    }

    // ============================================
    // ACTIVITY LOGGING CONFIGURATION
    // ============================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'description',
                'status',
                'is_visible',
                'hidden_due_to_subscription',
                'hidden_due_to_role_change',
                'base_price',
                'category_id',
                'listing_type_id',
            ])
            ->logOnlyDirty()            // Only log changed attributes
            ->dontSubmitEmptyLogs()     // Don't log if nothing changed
            ->setDescriptionForEvent(fn (string $eventName) => "Listing {$eventName}");
    }
}
