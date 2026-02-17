<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Slider;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Get the 'sell' listing type
        $sellType = ListingType::where('slug', 'sell')->first();

        // Filter only 'sell' type listings by default
        $featuredListings = Listing::publiclyVisible()
            ->where('is_featured', true)
            ->when($sellType, function ($query) use ($sellType) {
                return $query->where('listing_type_id', $sellType->id);
            })
            ->with(['category', 'listingType', 'primaryImage', 'firstImage', 'user'])
            ->latest()
            ->limit(15)
            ->get();

        $latestListings = Listing::publiclyVisible()
            ->when($sellType, function ($query) use ($sellType) {
                return $query->where('listing_type_id', $sellType->id);
            })
            ->with(['category', 'listingType', 'primaryImage', 'firstImage', 'user'])
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

        $mainSliders = Slider::active()
            ->mainSliders()
            ->ordered()
            ->limit(5)
            ->get();

        $smallBanners = Slider::active()
            ->smallBanners()
            ->ordered()
            ->limit(2)
            ->get();

        return view('welcome', compact(
            'featuredListings',
            'latestListings',
            'categories',
            'listingTypes',
            'mainSliders',
            'smallBanners'
        ));
    }
}
