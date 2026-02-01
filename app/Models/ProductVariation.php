<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductVariation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'listing_id',
        'sku',
        'variant_combination',
        'price',
        'discount_price',
        'discount_start_date',
        'discount_end_date',
        'cost_price',
        'price_adjustment',
        'stock_quantity',
        'low_stock_threshold',
        'manage_stock',
        'allow_backorder',
        'weight',
        'length',
        'width',
        'height',
        'dimensions',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'variant_combination' => 'array',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
        'cost_price' => 'decimal:2',
        'price_adjustment' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'manage_stock' => 'boolean',
        'allow_backorder' => 'boolean',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'dimensions' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==================== Relationships ====================

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductVariationAttribute::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            VariantItem::class,
            'product_variation_attributes',
            'product_variation_id',
            'variant_item_id'
        )->withPivot('variant_id');
    }

    public function images()
    {
        return $this->hasMany(ProductVariationImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductVariationImage::class)->where('is_primary', true);
    }

    public function firstImage()
    {
        return $this->hasOne(ProductVariationImage::class)->oldestOfMany('sort_order');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_variation_id');
    }

    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject');
    }

    // ==================== Scopes ====================

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('manage_stock', false)
                ->orWhere('stock_quantity', '>', 0)
                ->orWhere('allow_backorder', true);
        });
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->where('manage_stock', true);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('manage_stock', true)
            ->where('stock_quantity', '<=', 0)
            ->where('allow_backorder', false);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ==================== Methods ====================

    public function isInStock(): bool
    {
        if (! $this->manage_stock) {
            return true;
        }

        return $this->stock_quantity > 0 || $this->allow_backorder;
    }

    public function isLowStock(): bool
    {
        if (! $this->manage_stock) {
            return false;
        }

        return $this->stock_quantity <= $this->low_stock_threshold && $this->stock_quantity > 0;
    }

    public function getCurrentPrice(): float
    {
        if ($this->discount_price &&
            $this->discount_start_date &&
            $this->discount_end_date &&
            $this->discount_start_date <= now() &&
            $this->discount_end_date >= now()) {
            return (float) $this->discount_price;
        }

        return (float) $this->price;
    }

    public function hasActiveDiscount(): bool
    {
        return $this->discount_price &&
            $this->discount_start_date &&
            $this->discount_end_date &&
            $this->discount_start_date <= now() &&
            $this->discount_end_date >= now();
    }

    public function getDiscountPercentage(): ?float
    {
        if (! $this->hasActiveDiscount() || $this->price <= 0) {
            return null;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100, 2);
    }

    public function adjustStock(int $quantity, string $type, ?string $reference = null, ?string $notes = null): void
    {
        $oldQuantity = $this->stock_quantity;
        $this->stock_quantity += $quantity;
        $this->save();

        // Get authenticated user ID from either admin or regular guard
        $userId = auth()->guard('admin')->id() ?? auth()->id();

        StockMovement::create([
            'product_variation_id' => $this->id,
            'user_id' => $userId,
            'type' => $type,
            'quantity_change' => $quantity,
            'quantity_before' => $oldQuantity,
            'quantity_after' => $this->stock_quantity,
            'reference' => $reference,
            'notes' => $notes,
        ]);
    }

    public function decreaseStock(int $quantity, ?string $reference = null): bool
    {
        if (! $this->isInStock()) {
            return false;
        }

        if ($this->manage_stock && $this->stock_quantity < $quantity && ! $this->allow_backorder) {
            return false;
        }

        $this->adjustStock(-$quantity, 'sale', $reference);

        return true;
    }

    public function increaseStock(int $quantity, string $type = 'purchase', ?string $reference = null): void
    {
        $this->adjustStock($quantity, $type, $reference);
    }

    // Get variant items for this variation (from new attributes table)
    public function getVariantItemsAttribute()
    {
        return $this->attributeValues;
    }

    public function getFormattedAttributesAttribute(): array
    {
        return $this->attributes->mapWithKeys(function ($attr) {
            return [$attr->variant->name => $attr->variantItem->value];
        })->all();
    }

    // ==================== Activity Log ====================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'sku',
                'price',
                'discount_price',
                'stock_quantity',
                'is_active',
                'is_default',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
