<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationImage extends Model
{
    use HasFactory;

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
