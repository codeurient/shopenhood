<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * Deduct stock when order is created in a paid status
     */
    public function created(Order $order): void
    {
        // Only deduct stock if order is created in paid or processing status
        if ($order->shouldDeductStock() && $order->variation_id) {
            $this->deductStock($order);
        }
    }

    /**
     * Handle the Order "updated" event.
     *
     * Manage stock based on status changes
     */
    public function updated(Order $order): void
    {
        // Check if status changed
        if (! $order->wasChanged('status') && ! $order->wasChanged('payment_status')) {
            return;
        }

        $oldStatus = $order->getOriginal('status');
        $newStatus = $order->status;

        // If order was just paid, deduct stock
        if ($order->wasChanged('payment_status') && $order->payment_status === 'paid' && $order->variation_id) {
            $this->deductStock($order);
        }

        // If order was cancelled or refunded, restore stock
        if (($newStatus === 'cancelled' || $order->payment_status === 'refunded') &&
            ! in_array($oldStatus, ['cancelled']) &&
            $order->variation_id) {
            $this->restoreStock($order);
        }
    }

    /**
     * Handle the Order "deleted" event.
     *
     * Restore stock when order is deleted
     */
    public function deleted(Order $order): void
    {
        // Restore stock if order had reserved stock
        if ($order->variation_id && $order->shouldDeductStock()) {
            $this->restoreStock($order, 'Order deleted');
        }
    }

    /**
     * Deduct stock for an order
     */
    protected function deductStock(Order $order): void
    {
        try {
            $variation = $order->variation;

            if (! $variation || ! $variation->manage_stock) {
                return;
            }

            // Check if we have enough stock
            if ($variation->stock_quantity < $order->quantity && ! $variation->allow_backorder) {
                Log::warning('Insufficient stock for order', [
                    'order_id' => $order->id,
                    'variation_id' => $variation->id,
                    'requested' => $order->quantity,
                    'available' => $variation->stock_quantity,
                ]);

                return;
            }

            // Deduct stock and record movement
            $variation->adjustStock(
                -$order->quantity,
                'sale',
                "Order #{$order->order_number}",
                'Stock deducted for order placement'
            );

            Log::info('Stock deducted for order', [
                'order_id' => $order->id,
                'variation_id' => $variation->id,
                'quantity' => $order->quantity,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to deduct stock for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Restore stock for an order
     */
    protected function restoreStock(Order $order, ?string $reason = null): void
    {
        try {
            $variation = $order->variation;

            if (! $variation || ! $variation->manage_stock) {
                return;
            }

            // Restore stock and record movement
            $variation->adjustStock(
                $order->quantity,
                'return',
                "Order #{$order->order_number}",
                $reason ?? "Stock restored - Order {$order->status}"
            );

            Log::info('Stock restored for order', [
                'order_id' => $order->id,
                'variation_id' => $variation->id,
                'quantity' => $order->quantity,
                'reason' => $reason ?? $order->status,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to restore stock for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
