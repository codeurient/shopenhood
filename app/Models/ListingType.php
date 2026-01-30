<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'requires_price',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_price' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Listings of this type (sell, buy, gift, barter, auction)
     */
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Scope to get only active listing types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
