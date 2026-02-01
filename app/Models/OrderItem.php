<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'listing_id',
        'variation_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ==================== Relationships ====================

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }

    // ==================== Helper Methods ====================

    /**
     * Get the product name (listing title + variant info)
     */
    public function getProductName(): string
    {
        $name = $this->listing->title;

        if ($this->variation) {
            $variantInfo = $this->variation->attributes->map(function ($attr) {
                return $attr->variantItem->value;
            })->implode(' / ');

            if ($variantInfo) {
                $name .= ' ('.$variantInfo.')';
            }
        }

        return $name;
    }

    /**
     * Get the SKU for this order item
     */
    public function getSku(): ?string
    {
        return $this->variation?->sku;
    }

    /**
     * Calculate line total
     */
    public function calculateSubtotal(): float
    {
        return (float) ($this->quantity * $this->unit_price);
    }
}
