<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;

class ListingService
{
    // ============================================
    // LISTING LIMITS
    // ============================================

    /**
     * Check if user can create a new listing.
     * Normal users: count of all non-trashed listings (any status) must be 0.
     * Business users: count vs listing_limit (null = unlimited).
     */
    public function canUserCreateListing(User $user): bool
    {
        $limit = $user->getListingLimit();

        // null = unlimited
        if ($limit === null) {
            return true;
        }

        $currentCount = Listing::forUser($user->id)->count();

        return $currentCount < $limit;
    }

    /**
     * Check if user can create a normal (simple) listing.
     * Always capped at 1 regardless of business status.
     */
    public function canUserCreateNormalListing(User $user): bool
    {
        return Listing::normalMode()->forUser($user->id)->count() < 1;
    }

    /**
     * Check if user can reshare a trashed normal listing.
     */
    public function canReshareNormalListing(User $user, Listing $listing): bool
    {
        if (! $listing->trashed()) {
            return false;
        }

        if (! $listing->belongsToUser($user->id)) {
            return false;
        }

        return $this->canUserCreateNormalListing($user);
    }

    /**
     * Get remaining listing slots for a user.
     * Returns null for unlimited.
     */
    public function getRemainingListingSlots(User $user): ?int
    {
        $limit = $user->getListingLimit();

        if ($limit === null) {
            return null;
        }

        $currentCount = Listing::forUser($user->id)->count();

        return max(0, $limit - $currentCount);
    }

    // ============================================
    // EXPIRATION
    // ============================================

    public function getDefaultDurationDays(): int
    {
        return Setting::getValue('listing.default_duration_days', 30);
    }

    public function getSoftDeleteRetentionDays(): int
    {
        return Setting::getValue('listing.soft_delete_retention_days', 30);
    }

    public function calculateExpiresAt(?int $durationDays = null): Carbon
    {
        $days = $durationDays ?? $this->getDefaultDurationDays();

        return now()->addDays($days);
    }

    /**
     * Soft delete active listings whose expires_at has passed.
     * Returns count of expired listings.
     */
    public function expireOverdueListings(): int
    {
        $listings = Listing::expired()->get();

        foreach ($listings as $listing) {
            $listing->update(['status' => 'expired']);
            $listing->delete(); // soft delete
        }

        return $listings->count();
    }

    /**
     * Permanently delete listings that were soft-deleted more than retention days ago.
     * Returns count of purged listings.
     */
    public function purgeOldDeletedListings(): int
    {
        $retentionDays = $this->getSoftDeleteRetentionDays();

        $listings = Listing::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays($retentionDays))
            ->get();

        foreach ($listings as $listing) {
            $listing->forceDelete();
        }

        return $listings->count();
    }

    // ============================================
    // RESHARING
    // ============================================

    /**
     * Check if a user can reshare a trashed listing.
     * Listing must be trashed, belong to user, and user must not have
     * created a newer non-trashed listing since this one was deleted.
     */
    public function canReshareListing(User $user, Listing $listing): bool
    {
        if (! $listing->trashed()) {
            return false;
        }

        if (! $listing->belongsToUser($user->id)) {
            return false;
        }

        // Check user can create (has capacity)
        if (! $this->canUserCreateListing($user)) {
            return false;
        }

        return true;
    }

    /**
     * Reshare a trashed listing: restore, set new expiration, set status to pending.
     */
    public function reshareListing(User $user, Listing $listing, ?int $durationDays = null): Listing
    {
        $listing->restore();

        $listing->update([
            'status' => 'pending',
            'expires_at' => $this->calculateExpiresAt($durationDays),
        ]);

        return $listing->fresh();
    }

    // ============================================
    // SLUG PROTECTION
    // ============================================

    /**
     * Check slug availability including trashed listings.
     */
    public function isSlugAvailable(string $slug, ?int $excludeId = null): bool
    {
        $query = Listing::withTrashed()->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return ! $query->exists();
    }

    // ============================================
    // DELETION
    // ============================================

    public function softDeleteListing(User $user, Listing $listing): void
    {
        $listing->delete();
    }

    /**
     * Force delete a listing and all its associated files. Only for already-trashed listings.
     */
    public function forceDeleteListing(User $user, Listing $listing): void
    {
        if ($listing->trashed()) {
            // Delete listing image files (model events fire, cleaning up storage)
            $listing->images->each->delete();

            // Delete variation image files via model events
            $listing->variations()->with('images')->get()->each(function ($variation): void {
                $variation->images->each->delete();
            });

            $listing->forceDelete();
        }
    }

    // ============================================
    // VISIBILITY TOGGLE
    // ============================================

    /**
     * Toggle is_visible. Does NOT affect listing count for limit checks.
     */
    public function toggleListingVisibility(User $user, Listing $listing): void
    {
        $listing->update(['is_visible' => ! $listing->is_visible]);
    }
}
