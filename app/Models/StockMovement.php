<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_variation_id',
        'user_id',
        'order_id',
        'type',
        'quantity_change',
        'quantity_before',
        'quantity_after',
        'reference',
        'notes',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'created_at' => 'datetime',
    ];

    // ==================== Relationships ====================

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ==================== Scopes ====================

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePurchases($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeReturns($query)
    {
        return $query->where('type', 'return');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjustment');
    }

    // ==================== Methods ====================

    public function isIncrease(): bool
    {
        return $this->quantity_change > 0;
    }

    public function isDecrease(): bool
    {
        return $this->quantity_change < 0;
    }

    public function getAbsoluteQuantityAttribute(): int
    {
        return abs($this->quantity_change);
    }
}
