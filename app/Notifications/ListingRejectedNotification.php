<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ListingRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public Listing $listing, public string $reason) {}

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
            'type' => 'listing_rejected',
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'listing_slug' => $this->listing->slug,
            'rejection_reason' => $this->reason,
            'message' => "Your listing \"{$this->listing->title}\" was not approved.",
        ];
    }
}
