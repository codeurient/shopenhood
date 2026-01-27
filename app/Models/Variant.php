<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variant extends Model
{
    use HasFactory, LogsActivity;

    // Variant items (predefined options like "Red", "Large", "64GB")
    public function items()
    {
        return $this->hasMany(VariantItem::class)->orderBy('sort_order');
    }

    // Active variant items only
    public function activeItems()
    {
        return $this->hasMany(VariantItem::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // Categories this variant is assigned to (many-to-many through pivot)
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_variants')
            ->withPivot('is_required', 'is_searchable', 'is_filterable', 'sort_order')
            ->withTimestamps();
    }

    // Direct access to category_variants pivot records
    public function categoryVariants()
    {
        return $this->hasMany(CategoryVariant::class);
    }

    // Listing variants using this variant
    public function listingVariants()
    {
        return $this->hasMany(ListingVariant::class);
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
            ->logOnly(['name', 'slug', 'type', 'is_required', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
