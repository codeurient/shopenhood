<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'type',
        'code',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function cities()
    {
        return $this->hasMany(Location::class, 'parent_id')
            ->where('type', 'city');
    }

    public function scopeCountries($query)
    {
        return $query->where('type', 'country');
    }

    public function scopeOfTypeCity($query)
    {
        return $query->where('type', 'city');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
