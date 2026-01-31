<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use HasFactory, LogsActivity;

    // Allow mass assignment for these fields
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
        'level',
        'path',
        'is_active',
    ];

    // Self-referential relationship (parent category)
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Self-referential relationship (child categories)
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Recursive relationship (all descendants)
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // All ancestors (calculated from path, not a direct relationship)
    // Use accessor method instead: getAncestorsAttribute()

    // Listings in this category
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    // Variants assigned to this category (many-to-many through pivot)
    public function variants()
    {
        return $this->belongsToMany(Variant::class, 'category_variants')
            ->withPivot('is_required', 'is_searchable', 'is_filterable', 'sort_order')
            ->withTimestamps()
            ->orderBy('category_variants.sort_order');
    }

    // Direct access to category_variants pivot records
    public function categoryVariants()
    {
        return $this->hasMany(CategoryVariant::class);
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
            ->logOnly(['name', 'slug', 'parent_id', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
