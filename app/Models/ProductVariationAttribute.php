<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationAttribute extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_variation_id',
        'variant_id',
        'variant_item_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ==================== Relationships ====================

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function variantItem()
    {
        return $this->belongsTo(VariantItem::class);
    }
}
