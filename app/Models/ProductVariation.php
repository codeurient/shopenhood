<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariation extends Model
{
    use HasFactory, LogsActivity;

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
                'is_available'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
