<?php

namespace App\Notifications;

use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConfidentSellerRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public BusinessProfile $businessProfile,
        public string $reason
    ) {}

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
        return [
            'type' => 'confident_seller_rejected',
            'business_profile_id' => $this->businessProfile->id,
            'business_name' => $this->businessProfile->business_name,
            'rejection_reason' => $this->reason,
            'message' => "Your Confident Seller application for \"{$this->businessProfile->business_name}\" was not approved.",
        ];
    }
}
