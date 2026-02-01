<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Variant extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_required',
        'description',
        'placeholder',
        'help_text',
        'sort_order',
        'is_active',
    ];

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
            ->withPivot('is_required', 'is_searchable', 'is_filterable', 'is_main_shown', 'sort_order')
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
