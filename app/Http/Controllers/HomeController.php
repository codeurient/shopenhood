<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredListings = Listing::publiclyVisible()
            ->where('is_featured', true)
            ->with(['category', 'listingType', 'primaryImage', 'firstImage'])
            ->latest()
            ->limit(8)
            ->get();

        $latestListings = Listing::publiclyVisible()
            ->with(['category', 'listingType', 'primaryImage', 'firstImage'])
            ->latest()
            ->limit(8)
            ->get();

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->withCount(['listings' => function ($query) {
                $query->where('status', 'active')->where('is_visible', true);
            }])
            ->orderBy('sort_order')
            ->get();

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('welcome', compact(
            'featuredListings',
            'latestListings',
            'categories',
            'listingTypes'
        ));
    }
}
