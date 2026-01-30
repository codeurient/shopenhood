<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductVariation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'listing_id',
        'sku',
        'variant_combination',
        'price_adjustment',
        'stock_quantity',
        'low_stock_threshold',
        'is_available',
        'weight',
        'dimensions',
    ];

    protected $casts = [
        'variant_combination' => 'array',
        'price_adjustment' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_available' => 'boolean',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
    ];

    // Parent listing
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Orders for this specific variation
    public function orders()
    {
        return $this->hasMany(Order::class, 'variation_id');
    }

    // Order items for this variation
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variation_id');
    }

    // Images for this product variation
    public function images()
    {
        return $this->hasMany(ProductVariationImage::class)->orderBy('sort_order');
    }

    // Primary/featured image for this variation
    public function primaryImage()
    {
        return $this->hasOne(ProductVariationImage::class)
            ->where('is_primary', true);
    }

    // First image (fallback if no primary)
    public function firstImage()
    {
        return $this->hasOne(ProductVariationImage::class)
            ->oldestOfMany('sort_order');
    }

    // Get variant items for this variation (from JSON variant_combination)
    // This requires a custom accessor or helper method, not a direct relationship
    // Example: getVariantItemsAttribute() that decodes JSON and fetches items
    public function getVariantItemsAttribute()
    {
        $variantIds = collect(json_decode($this->variant_combination))
            ->flatten()   // bütün ID-ləri bir array-a çevirir
            ->toArray();

        return VariantItem::whereIn('id', $variantIds)->get();
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
            ->logOnly([
                'sku',
                'price_adjustment',
                'stock_quantity',
                'is_available',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
