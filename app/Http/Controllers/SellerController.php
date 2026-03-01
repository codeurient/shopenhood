<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingReview;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;

class SellerController extends Controller
{
    public function show(User $user): View
    {
        abort_if($user->status !== 'active' || $user->isAdmin(), 404);

        $user->load('businessProfile');

        $listings = Listing::publiclyVisible()
            ->where('user_id', $user->id)
            ->with([
                'primaryImage', 'firstImage',
                'defaultVariation', 'defaultVariation.primaryImage', 'defaultVariation.firstImage',
                'category', 'listingType',
                'user.businessProfile',
            ])
            ->latest()
            ->paginate(12);

        $totalSold = (int) Order::where('seller_id', $user->id)
            ->whereNotIn('status', ['cancelled'])
            ->sum('quantity');

        $avgRating = round(
            ListingReview::whereHas('listing', fn ($q) => $q->where('user_id', $user->id))
                ->avg('rating') ?? 0,
            1
        );

        $listingCount = Listing::publiclyVisible()->where('user_id', $user->id)->count();

        $sellerBadge = null;
        if ($totalSold >= 100000) {
            $sellerBadge = ['label' => 'Expert Seller', 'color' => 'text-yellow-500'];
        } elseif ($totalSold >= 50000) {
            $sellerBadge = ['label' => 'Top Seller', 'color' => 'text-red-400'];
        } elseif ($totalSold >= 10000) {
            $sellerBadge = ['label' => 'Rising Seller', 'color' => 'text-blue-400'];
        }

        return view('sellers.show', compact('user', 'listings', 'totalSold', 'avgRating', 'listingCount', 'sellerBadge'));
    }
}
