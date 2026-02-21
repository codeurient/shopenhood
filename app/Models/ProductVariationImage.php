<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductVariationImage extends Model
{
    use HasFactory;

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (ProductVariationImage $image): void {
            Storage::disk('public')->delete($image->image_path);
        });
    }

    protected $fillable = [
        'product_variation_id',
        'image_path',
        'original_filename',
        'file_size',
        'mime_type',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Get the product variation this image belongs to
     */
    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
