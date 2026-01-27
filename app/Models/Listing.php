<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Listing extends Model
{
    use HasFactory;
    use LogsActivity, SoftDeletes;

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

    // Available variations only
    public function availableVariations()
    {
        return $this->hasMany(ProductVariation::class)
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0);
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
        return $this->hasMany(Review::class);
    }

    // Approved reviews only
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)
            ->where('status', 'approved');
    }

    // Users who favorited this listing
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
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
                'base_price',
                'category_id',
                'listing_type_id',
            ])
            ->logOnlyDirty()            // Only log changed attributes
            ->dontSubmitEmptyLogs()     // Don't log if nothing changed
            ->setDescriptionForEvent(fn (string $eventName) => "Listing {$eventName}");
    }
}
