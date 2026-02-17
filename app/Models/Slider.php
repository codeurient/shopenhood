<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMainSliders($query)
    {
        return $query->where('type', 'main_slider');
    }

    public function scopeSmallBanners($query)
    {
        return $query->where('type', 'banner_small');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
