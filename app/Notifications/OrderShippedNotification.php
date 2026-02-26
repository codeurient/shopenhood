<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderShippedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = "Your order {$this->order->order_number} has been shipped.";

        if ($this->order->tracking_number) {
            $message .= " Tracking: {$this->order->tracking_number}";
        }

        return [
            'type' => 'order_shipped',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'listing_title' => $this->order->listing?->title ?? 'Deleted listing',
            'tracking_number' => $this->order->tracking_number,
            'message' => $message,
        ];
    }
}
