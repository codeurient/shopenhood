<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\ProductVariation;
use App\Models\SearchQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::publiclyVisible()
            ->with(['category', 'listingType', 'primaryImage', 'firstImage']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('type')) {
            $query->where('listing_type_id', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'newest');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            default => $query->latest(),
        };

        $listings = $query->paginate(12)->withQueryString();

        // Log search query
        if ($request->filled('search')) {
            SearchQuery::create([
                'query' => $request->search,
                'user_id' => auth()->id(),
                'results_count' => $listings->total(),
                'filters' => array_filter([
                    'category' => $request->category,
                    'type' => $request->type,
                    'min_price' => $request->min_price,
                    'max_price' => $request->max_price,
                    'sort' => $sort,
                ]),
            ]);
        }

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('listings.index', compact('listings', 'categories', 'listingTypes'));
    }

    /**
     * Display the listing detail page
     */
    public function show(Listing $listing)
    {
        // Load listing with relationships
        $listing->load([
            'category',
            'listingType',
            'images',
            'variations' => function ($query) {
                $query->active()
                    ->with([
                        'attributes.variant',
                        'attributes.variantItem',
                        'images',
                        'primaryImage',
                    ])
                    ->orderBy('sort_order');
            },
            'variations.attributes.variant.items',
        ]);

        // Get category variants for display
        $categoryVariants = $listing->category->allVariants()
            ->with(['items' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->get();

        // Get default or first variation
        $defaultVariation = $listing->defaultVariation ?? $listing->variations->first();

        // Prepare variations data for JavaScript
        $variationsData = $listing->variations->map(function ($variation) {
            return [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'price' => (float) $variation->price,
                'discount_price' => $variation->discount_price ? (float) $variation->discount_price : null,
                'current_price' => $variation->getCurrentPrice(),
                'discount_percentage' => $variation->getDiscountPercentage(),
                'has_discount' => $variation->hasActiveDiscount(),
                'stock_quantity' => $variation->stock_quantity,
                'is_in_stock' => $variation->isInStock(),
                'is_low_stock' => $variation->isLowStock(),
                'is_default' => $variation->is_default,
                'attributes' => $variation->attributes->map(function ($attr) {
                    return [
                        'variant_id' => $attr->variant_id,
                        'variant_item_id' => $attr->variant_item_id,
                        'variant_name' => $attr->variant->name,
                        'item_value' => $attr->variantItem->value,
                    ];
                })->toArray(),
                'images' => $variation->images->map(function ($img) {
                    return [
                        'url' => asset('storage/'.$img->image_path),
                        'is_primary' => $img->is_primary,
                    ];
                })->toArray(),
            ];
        });

        // Prepare variants data for display
        $variantsData = $categoryVariants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'display_type' => $variant->display_type,
                'is_required' => $variant->pivot->is_required,
                'items' => $variant->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'value' => $item->value,
                        'color_code' => $item->color_code,
                        'image_path' => $item->image_path,
                    ];
                })->toArray(),
            ];
        });

        return view('listings.show', compact(
            'listing',
            'defaultVariation',
            'variationsData',
            'variantsData'
        ));
    }

    /**
     * Get available variant options based on current selection
     */
    public function getAvailableOptions(Request $request, Listing $listing)
    {
        $selectedOptions = $request->input('selected', []);

        // Get variations that match current selection and are in stock
        $matchingVariations = $listing->variations()
            ->active()
            ->inStock()
            ->where(function ($query) use ($selectedOptions) {
                foreach ($selectedOptions as $variantId => $itemId) {
                    $query->whereHas('attributes', function ($q) use ($variantId, $itemId) {
                        $q->where('variant_id', $variantId)
                            ->where('variant_item_id', $itemId);
                    });
                }
            })
            ->with('attributes')
            ->get();

        // Build available options map
        $availableOptions = [];
        foreach ($matchingVariations as $variation) {
            foreach ($variation->attributes as $attr) {
                if (! isset($availableOptions[$attr->variant_id])) {
                    $availableOptions[$attr->variant_id] = [];
                }
                $availableOptions[$attr->variant_id][] = $attr->variant_item_id;
            }
        }

        // Deduplicate
        foreach ($availableOptions as $variantId => $items) {
            $availableOptions[$variantId] = array_values(array_unique($items));
        }

        return response()->json([
            'success' => true,
            'available_options' => $availableOptions,
            'matching_variations_count' => $matchingVariations->count(),
        ]);
    }

    /**
     * Get specific variation details
     */
    public function getVariation(ProductVariation $variation)
    {
        $variation->load(['attributes.variant', 'attributes.variantItem', 'images']);

        return response()->json([
            'success' => true,
            'variation' => [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'price' => (float) $variation->price,
                'discount_price' => $variation->discount_price ? (float) $variation->discount_price : null,
                'current_price' => $variation->getCurrentPrice(),
                'discount_percentage' => $variation->getDiscountPercentage(),
                'stock_quantity' => $variation->stock_quantity,
                'is_in_stock' => $variation->isInStock(),
                'is_low_stock' => $variation->isLowStock(),
                'images' => $variation->images->map(function ($img) {
                    return [
                        'url' => asset('storage/'.$img->image_path),
                        'is_primary' => $img->is_primary,
                    ];
                }),
            ],
        ]);
    }
}
