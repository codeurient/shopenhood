<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'variation_id',
        'quantity',
        'is_selected',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'is_selected' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class)->withTrashed();
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }

    /** Unit price: discount price if active, otherwise base price. */
    public function getUnitPriceAttribute(): float
    {
        $listing = $this->listing;

        if (
            $listing->discount_price &&
            $listing->discount_start_date <= now() &&
            $listing->discount_end_date >= now()
        ) {
            return (float) $listing->discount_price;
        }

        return (float) ($listing->base_price ?? 0);
    }

    /** Delivery cost for this item (domestic delivery price as default). */
    public function getDeliveryCostAttribute(): float
    {
        $listing = $this->listing;

        if (! $listing->has_delivery) {
            return 0.0;
        }

        if ($listing->has_domestic_delivery && $listing->domestic_delivery_price > 0) {
            return (float) $listing->domestic_delivery_price;
        }

        return 0.0;
    }

    public function getLineTotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }
}
