<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Listing;
use App\Models\ListingReview;
use App\Models\ListingType;
use App\Models\Order;
use App\Models\ProductVariation;
use App\Models\SearchQuery;
use App\Models\Variant;
use App\Models\VariantItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::publiclyVisible()
            ->with(['category', 'listingType', 'primaryImage', 'firstImage', 'user.businessProfile']);

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
            'user.businessProfile',
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

        // Always include the Basic Information as a selectable option when a base_price exists.
        // It is the default when no real ProductVariation has is_default = true.
        if ($listing->base_price) {
            $listingHasDiscount = $listing->discount_price
                && $listing->discount_start_date
                && $listing->discount_end_date
                && now()->between($listing->discount_start_date, $listing->discount_end_date);

            $basicIsDefault = ! $variationsData->contains('is_default', true);

            $basicVariation = [[
                'id' => null,
                'sku' => null,
                'price' => (float) $listing->base_price,
                'discount_price' => $listingHasDiscount ? (float) $listing->discount_price : null,
                'current_price' => $listingHasDiscount ? (float) $listing->discount_price : (float) $listing->base_price,
                'discount_percentage' => $listingHasDiscount
                    ? (int) round((($listing->base_price - $listing->discount_price) / $listing->base_price) * 100)
                    : 0,
                'has_discount' => (bool) $listingHasDiscount,
                'stock_quantity' => null,
                'is_in_stock' => true,
                'is_low_stock' => false,
                'is_default' => $basicIsDefault,
                'attributes' => [],
                'images' => [],
            ]];

            // Prepend so the base product always appears first.
            $variationsData = collect($basicVariation)->concat($variationsData);
        }

        // Build variant selectors from the listing's actual variation attributes.
        // This ensures selectors appear even when the category has no variants assigned,
        // and only shows options that actually exist in this listing's variations.
        $variantsMap = [];
        foreach ($listing->variations as $variation) {
            foreach ($variation->attributes as $attr) {
                $vid = $attr->variant_id;
                if (! isset($variantsMap[$vid])) {
                    $variantsMap[$vid] = [
                        'id' => $vid,
                        'name' => $attr->variant->name,
                        'display_type' => $attr->variant->display_type ?? 'button',
                        'items' => [],
                        'seen_item_ids' => [],
                    ];
                }
                if (! in_array($attr->variant_item_id, $variantsMap[$vid]['seen_item_ids'])) {
                    $variantsMap[$vid]['items'][] = [
                        'id' => $attr->variant_item_id,
                        'value' => $attr->variantItem->value,
                        'color_code' => $attr->variantItem->color_code ?? null,
                        'image_path' => $attr->variantItem->image_path ?? null,
                    ];
                    $variantsMap[$vid]['seen_item_ids'][] = $attr->variant_item_id;
                }
            }
        }

        $variantsData = collect(array_values($variantsMap))->map(function ($v) {
            unset($v['seen_item_ids']);

            return $v;
        });

        $relatedListings = Listing::publiclyVisible()
            ->where('category_id', $listing->category_id)
            ->where('id', '!=', $listing->id)
            ->with([
                'primaryImage', 'firstImage', 'user',
                'defaultVariation.primaryImage', 'defaultVariation.firstImage',
                'location.parent',
            ])
            ->latest()
            ->limit(6)
            ->get();

        // Resolve variant_attributes JSON to human-readable labels for the show view
        $variantAttributeLabels = [];
        if (! empty($listing->variant_attributes)) {
            $variantIds = array_keys($listing->variant_attributes);
            $itemIds = array_values($listing->variant_attributes);

            $variants = Variant::whereIn('id', $variantIds)->get()->keyBy('id');
            $items = VariantItem::whereIn('id', $itemIds)->get()->keyBy('id');

            foreach ($listing->variant_attributes as $variantId => $itemId) {
                if (isset($variants[$variantId]) && isset($items[$itemId])) {
                    $variantAttributeLabels[$variants[$variantId]->name] = $items[$itemId]->value;
                }
            }
        }

        $reviews = collect();
        $canReview = false;
        $alreadyReviewed = false;

        if ($listing->listing_mode === 'business') {
            $reviews = ListingReview::where('listing_id', $listing->id)
                ->with('user')
                ->latest()
                ->get();

            $user = auth()->user();

            if ($user) {
                $alreadyReviewed = ListingReview::where('listing_id', $listing->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (! $alreadyReviewed) {
                    $canReview = Order::where('listing_id', $listing->id)
                        ->where('buyer_id', $user->id)
                        ->whereIn('status', ['delivered', 'completed'])
                        ->exists();
                }
            }
        }

        return view('listings.show', compact(
            'listing',
            'defaultVariation',
            'variationsData',
            'variantsData',
            'variantAttributeLabels',
            'relatedListings',
            'reviews',
            'canReview',
            'alreadyReviewed'
        ));
    }

    /**
     * Validate a coupon code against a specific listing and return the discount amount.
     */
    public function validateCoupon(Request $request, Listing $listing): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:50']);

        $coupon = Coupon::where('code', $request->code)->first();

        if (! $coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid coupon code.'], 422);
        }

        if (! $coupon->is_active) {
            return response()->json(['success' => false, 'message' => 'This coupon is no longer active.'], 422);
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return response()->json(['success' => false, 'message' => 'This coupon is not yet valid.'], 422);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'This coupon has expired.'], 422);
        }

        if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
            return response()->json(['success' => false, 'message' => 'The limit has been reached.'], 422);
        }

        if ($coupon->per_user_limit !== null && auth()->check()) {
            $userUsageCount = $coupon->usages()->where('user_id', auth()->id())->count();
            if ($userUsageCount >= $coupon->per_user_limit) {
                return response()->json(['success' => false, 'message' => 'You have already used this coupon the maximum number of times.'], 422);
            }
        }

        if ($coupon->applicable_to === 'listings') {
            $isRestricted = $coupon->restrictions()
                ->where('restrictable_type', Listing::class)
                ->where('restrictable_id', $listing->id)
                ->exists();
            if (! $isRestricted) {
                return response()->json(['success' => false, 'message' => 'This coupon is not applicable to this product.'], 422);
            }
        } elseif ($coupon->applicable_to === 'categories') {
            $isRestricted = $coupon->restrictions()
                ->where('restrictable_type', Category::class)
                ->where('restrictable_id', $listing->category_id)
                ->exists();
            if (! $isRestricted) {
                return response()->json(['success' => false, 'message' => 'This coupon is not applicable to this category.'], 422);
            }
        } elseif ($coupon->applicable_to === 'users') {
            return response()->json(['success' => false, 'message' => 'This coupon is not applicable here.'], 422);
        }

        $price = (float) $request->input('price', $listing->base_price ?? 0);

        if ($coupon->min_purchase_amount !== null && $price < (float) $coupon->min_purchase_amount) {
            return response()->json([
                'success' => false,
                'message' => 'A minimum purchase of '.number_format($coupon->min_purchase_amount, 2).' is required.',
            ], 422);
        }

        if ($coupon->type === 'percentage') {
            $discount = $price * ((float) $coupon->value / 100);
            if ($coupon->max_discount_amount !== null) {
                $discount = min($discount, (float) $coupon->max_discount_amount);
            }
        } else {
            $discount = min((float) $coupon->value, $price);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
                'discount_amount' => round($discount, 2),
                'final_price' => round(max(0, $price - $discount), 2),
            ],
        ]);
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
