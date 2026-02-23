<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /** GET /my-favorites — Show the authenticated user's favorite listings. */
    public function index()
    {
        $listings = Auth::user()
            ->favoriteListings()
            ->with([
                'primaryImage',
                'firstImage',
                'defaultVariation.primaryImage',
                'defaultVariation.firstImage',
            ])
            ->publiclyVisible()
            ->latest('favorites.created_at')
            ->paginate(24);

        return view('user.favorites.index', compact('listings'));
    }

    /** POST /api/favorites/{listing} — Toggle favorite status for a listing. */
    public function toggle(Listing $listing): JsonResponse
    {
        $result = Auth::user()->favoriteListings()->toggle($listing->id);
        $favorited = count($result['attached']) > 0;

        return response()->json(['favorited' => $favorited]);
    }
}
