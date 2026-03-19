<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Pre-computes per-listing and per-seller sold counts in bulk to prevent
 * N+1 queries when rendering listing cards. Register as a singleton so
 * the data is computed once per request.
 */
class ListingCardStatsService
{
    /** @var array<int, int> */
    private array $listingTotals = [];

    /** @var array<int, int> */
    private array $sellerTotals = [];

    /** @var int[]|null null = not yet loaded */
    private ?array $authFavoriteIds = null;

    /**
     * Bulk-fetch sold counts for the given listing and seller IDs.
     * Safe to call multiple times — already-loaded IDs are skipped.
     *
     * @param  Collection<int, int>  $listingIds
     * @param  Collection<int, int>  $sellerIds
     */
    public function warm(Collection $listingIds, Collection $sellerIds): void
    {
        $newListingIds = $listingIds->diff(array_keys($this->listingTotals))->values();
        $newSellerIds = $sellerIds->diff(array_keys($this->sellerTotals))->values();

        if ($newListingIds->isNotEmpty()) {
            DB::table('orders')
                ->whereIn('listing_id', $newListingIds)
                ->whereNotIn('status', ['cancelled'])
                ->groupBy('listing_id')
                ->select('listing_id', DB::raw('sum(quantity) as total'))
                ->get()
                ->each(fn ($row) => $this->listingTotals[(int) $row->listing_id] = (int) $row->total);

            // Ensure every requested listing_id has an entry (even if 0 sold)
            foreach ($newListingIds as $id) {
                $this->listingTotals[$id] ??= 0;
            }
        }

        if ($newSellerIds->isNotEmpty()) {
            DB::table('orders')
                ->whereIn('seller_id', $newSellerIds)
                ->whereNotIn('status', ['cancelled'])
                ->groupBy('seller_id')
                ->select('seller_id', DB::raw('sum(quantity) as total'))
                ->get()
                ->each(fn ($row) => $this->sellerTotals[(int) $row->seller_id] = (int) $row->total);

            foreach ($newSellerIds as $id) {
                $this->sellerTotals[$id] ??= 0;
            }
        }
    }

    public function getListingTotal(int $listingId): int
    {
        return $this->listingTotals[$listingId] ?? 0;
    }

    public function getSellerTotal(int $sellerId): int
    {
        return $this->sellerTotals[$sellerId] ?? 0;
    }

    /**
     * Returns the authenticated user's favorited listing IDs.
     * Loaded once per request via the singleton; returns [] for guests.
     *
     * @return int[]
     */
    public function getFavoriteIds(): array
    {
        if ($this->authFavoriteIds !== null) {
            return $this->authFavoriteIds;
        }

        if (! auth()->check()) {
            return $this->authFavoriteIds = [];
        }

        return $this->authFavoriteIds = auth()->user()
            ->favoriteListings()
            ->pluck('listings.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
