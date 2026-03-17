<?php

namespace App\Observers;

use App\Models\CartItem;
use App\Models\Listing;

class ListingObserver
{
    /**
     * Handle the Listing "deleted" event (soft delete).
     *
     * Remove all cart items referencing this listing so buyers are not
     * left with stale cart entries for an unavailable product.
     */
    public function deleted(Listing $listing): void
    {
        CartItem::where('listing_id', $listing->id)->delete();
    }

    /**
     * Handle the Listing "forceDeleted" event (permanent deletion).
     *
     * A force-delete bypasses the normal "deleted" flow when the model is
     * already trashed, so we handle cart cleanup explicitly here too.
     */
    public function forceDeleted(Listing $listing): void
    {
        CartItem::where('listing_id', $listing->id)->delete();
    }
}
