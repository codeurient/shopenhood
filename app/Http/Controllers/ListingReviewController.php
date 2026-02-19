<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingReviewRequest;
use App\Models\Listing;
use App\Models\ListingReview;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;

class ListingReviewController extends Controller
{
    public function store(StoreListingReviewRequest $request, Listing $listing): RedirectResponse
    {
        $user = auth()->user();

        // Check the user has a delivered or completed order for this listing
        $eligibleOrder = Order::where('listing_id', $listing->id)
            ->where('buyer_id', $user->id)
            ->whereIn('status', ['delivered', 'completed'])
            ->first();

        if (! $eligibleOrder) {
            return back()->with('error', 'You can only review products you have received.');
        }

        // Prevent duplicate reviews
        $alreadyReviewed = ListingReview::where('listing_id', $listing->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        ListingReview::create([
            'listing_id' => $listing->id,
            'user_id' => $user->id,
            'order_id' => $eligibleOrder->id,
            'rating' => $request->validated('rating'),
            'title' => $request->validated('title'),
            'body' => $request->validated('body'),
            'is_verified_purchase' => true,
        ]);

        return back()->with('success', 'Your review has been submitted. Thank you!');
    }

    public function destroy(ListingReview $review): RedirectResponse
    {
        abort_unless(auth()->id() === $review->user_id, 403);

        $review->delete();

        return back()->with('success', 'Your review has been removed.');
    }
}
