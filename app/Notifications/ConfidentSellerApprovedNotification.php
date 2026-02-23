<?php

namespace App\Notifications;

use App\Models\BusinessProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConfidentSellerApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public BusinessProfile $businessProfile) {}

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
            'type' => 'confident_seller_approved',
            'business_profile_id' => $this->businessProfile->id,
            'business_name' => $this->businessProfile->business_name,
            'message' => "Congratulations! Your business profile \"{$this->businessProfile->business_name}\" has been approved as a Confident Seller.",
        ];
    }
}
