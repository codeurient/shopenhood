<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ListingImage extends Model
{
    use HasFactory;

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (ListingImage $image): void {
            Storage::disk('public')->delete($image->image_path);
        });
    }

    protected $fillable = [
        'listing_id',
        'image_path',
        'thumbnail_path',
        'medium_path',
        'original_filename',
        'file_size',
        'mime_type',
        'width',
        'height',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'sort_order' => 'integer',
    ];

    // Parent listing
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
