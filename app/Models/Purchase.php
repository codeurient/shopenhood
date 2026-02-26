<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'buyer_id',
        'address_snapshot',
        'payment_method',
        'notes',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'currency',
        'status',
    ];

    protected $casts = [
        'address_snapshot' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeForBuyer($query, int $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public static function generatePurchaseNumber(): string
    {
        do {
            $number = 'PUR-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while (static::where('purchase_number', $number)->exists());

        return $number;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Derive and persist purchase status from the current state of its orders.
     *
     * Rules (non-cancelled orders drive the outcome):
     *   - all cancelled                → cancelled
     *   - all non-cancelled completed  → completed
     *   - any shipped/delivered        → processing
     *   - otherwise                    → pending
     */
    public function syncStatus(): void
    {
        $orders = $this->orders()->get();
        $nonCancelled = $orders->filter(fn ($o) => $o->status !== 'cancelled');

        if ($nonCancelled->isEmpty()) {
            $status = 'cancelled';
        } elseif ($nonCancelled->every(fn ($o) => in_array($o->status, ['completed', 'delivered']))) {
            // All fulfilled (delivered counts as completed — no separate UI action exists)
            $status = 'completed';
        } elseif ($nonCancelled->contains(fn ($o) => in_array($o->status, ['shipped', 'delivered', 'processing']))) {
            $status = 'processing';
        } else {
            $status = 'pending';
        }

        $this->update(['status' => $status]);
    }
}
