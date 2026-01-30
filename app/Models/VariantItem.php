<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VariantItem extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'variant_id',
        'value',
        'display_value',
        'color_code',
        'image',
        'sort_order',
        'is_active',
    ];

    // Parent variant (e.g., "Color" variant has item "Red")
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    // Listing variants that selected this item
    public function listingVariants()
    {
        return $this->hasMany(ListingVariant::class);
    }

    // Listings that use this variant item (through listing_variants)
    public function listings()
    {
        return $this->hasManyThrough(
            Listing::class,
            ListingVariant::class,
            'variant_item_id',  // Foreign key on listing_variants
            'id',               // Foreign key on listings
            'id',               // Local key on variant_items
            'listing_id'        // Local key on listing_variants
        );
    }

    public function activities()
    {
        return $this->morphMany(
            \Spatie\Activitylog\Models\Activity::class,
            'subject'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['value', 'display_value', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
