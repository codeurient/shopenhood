<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingVariant extends Model
{
    use HasFactory;

    // Parent listing
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    // Variant definition (e.g., "Color", "Size")
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    // Selected variant item (e.g., "Red", "Large")
    // Nullable for text/number type variants
    public function variantItem()
    {
        return $this->belongsTo(VariantItem::class);
    }
}
