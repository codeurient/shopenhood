<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    /**
     * Display a listing of all listings
     */
    public function index(Request $request)
    {
        $isDeletedFilter = $request->status === 'deleted';

        if ($isDeletedFilter) {
            $query = Listing::onlyTrashed()
                ->with(['user', 'category', 'listingType', 'images'])
                ->withCount(['images', 'variants', 'variations']);
        } else {
            $query = Listing::with(['user', 'category', 'listingType', 'images'])
                ->withCount(['images', 'variants', 'variations']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('slug', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by listing type
        if ($request->filled('listing_type_id')) {
            $query->where('listing_type_id', $request->listing_type_id);
        }

        $listings = $query->latest()
            ->paginate(20)
            ->withQueryString();

        // For filters
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Statistics
        $stats = [
            'total' => Listing::count(),
            'active' => Listing::where('status', 'active')->count(),
            'pending' => Listing::where('status', 'pending')->count(),
            'draft' => Listing::where('status', 'draft')->count(),
            'rejected' => Listing::where('status', 'rejected')->count(),
            'deleted' => Listing::onlyTrashed()->count(),
        ];

        return view('admin.listings.index', compact(
            'listings',
            'categories',
            'listingTypes',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new listing
     */
    public function create()
    {
        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.listings.create', compact('listingTypes'));
    }

    /**
     * Store a newly created listing
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_type_id' => 'required|exists:listing_types,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:500|unique:listings,slug',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'base_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:base_price',
            'discount_start_date' => 'nullable|date|required_with:discount_price',
            'discount_end_date' => 'nullable|date|after:discount_start_date|required_with:discount_price',
            'currency' => 'nullable|string|max:3',
            'is_negotiable' => 'nullable|boolean',
            'is_visible' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'condition' => 'required|in:new,used',
            'availability_type' => 'nullable|in:in_stock,available_by_order',
            'has_delivery' => 'nullable|boolean',
            'has_domestic_delivery' => 'nullable|boolean',
            'domestic_delivery_price' => 'nullable|numeric|min:0',
            'has_international_delivery' => 'nullable|boolean',
            'international_delivery_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending,active',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'store_name' => 'nullable|string|max:255',
            'created_as_role' => 'nullable|in:admin,normal_user,business_user',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',

            // Old variant values (for backward compatibility)
            'variants' => 'nullable|array',
            'variants.*' => 'nullable',

            // Product Variations (new system)
            'variations' => 'nullable|array',
            'variations.*.sku' => 'required|string|unique:product_variations,sku',
            'variations.*.attributes' => 'nullable|array',
            'variations.*.price' => 'required|numeric|min:0',
            'variations.*.discount_price' => 'nullable|numeric|min:0|lt:variations.*.price',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_default' => 'nullable|boolean',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.images' => 'nullable|array',
            'variations.*.images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',

            // Images
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'detail_images' => 'nullable|array',
            'detail_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // dd($validated);

        DB::beginTransaction();
        try {
            // Generate slug
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['title']);
            }
            $validated['slug'] = $this->generateUniqueSlug($validated['slug']);

            // Set defaults
            $validated['user_id'] = auth()->guard('admin')->id();
            $validated['created_as_role'] = $validated['created_as_role'] ?? 'admin';
            $validated['is_negotiable'] = $request->has('is_negotiable');
            $validated['is_visible'] = $request->has('is_visible') ? true : false;
            $validated['is_featured'] = $request->has('is_featured');
            $validated['has_delivery'] = $request->has('has_delivery');
            $validated['has_domestic_delivery'] = $request->has('has_domestic_delivery');
            $validated['has_international_delivery'] = $request->has('has_international_delivery');
            $validated['currency'] = $validated['currency'] ?? 'USD';
            $validated['availability_type'] = $validated['availability_type'] ?? 'in_stock';

            // Create listing
            $listing = Listing::create($validated);

            // Handle variant values
            if (! empty($request->variants)) {
                $this->attachVariants($listing, $request->variants);
            }

            // Handle main image
            if ($request->hasFile('main_image')) {
                $this->uploadImages($listing, [$request->file('main_image')]);
            }

            // Handle detail images
            if ($request->hasFile('detail_images')) {
                $this->uploadImages($listing, $request->file('detail_images'));
            }

            // Handle product variations (new system)
            if (! empty($request->variations)) {
                $this->createProductVariations($listing, $request->variations, $request);
            }

            DB::commit();

            activity()
                ->performedOn($listing)
                ->causedBy(auth()->guard('admin')->user())
                ->log('Listing created');

            return redirect()
                ->route('admin.listings.index')
                ->with('success', 'Listing created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Listing creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create listing: '.$e->getMessage());
        }
    }

    /**
     * Get variants for a specific category (AJAX)
     */
    public function getCategoryVariants(Request $request, Category $category)
    {
        try {
            // Check if we should show all variants or only main shown variants
            $showAll = $request->query('show_all', false);

            // Get variants assigned only to this specific category (not hierarchy)
            $variants = Variant::whereHas('categories', function ($query) use ($category, $showAll) {
                $query->where('categories.id', $category->id);

                // Filter by is_main_shown = true only for variation manager (not sidebar)
                if (! $showAll) {
                    $query->where('category_variants.is_main_shown', true);
                }
            })
                ->with([
                    'items' => function ($query) {
                        $query->where('is_active', true)->orderBy('sort_order');
                    },
                    'categories' => function ($query) use ($category) {
                        // Eager load ONLY this specific category with pivot data
                        $query->where('categories.id', $category->id);
                    },
                ])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'level' => $category->level,
                ],
                'variants' => $variants->map(function ($variant) {
                    // Get pivot data from the eager-loaded categories relationship
                    $categoryRelation = $variant->categories->first();
                    $pivot = $categoryRelation ? $categoryRelation->pivot : null;

                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'type' => $variant->type,
                        'is_required' => $pivot->is_required ?? false,
                        'is_main_shown' => $pivot->is_main_shown ?? false,
                        'placeholder' => $variant->placeholder,
                        'items' => $variant->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'value' => $item->value,
                                'display_value' => $item->display_value ?? $item->value,
                                'color_code' => $item->color_code,
                                'image' => $item->image,
                            ];
                        }),
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading category variants', [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading variants',
            ], 500);
        }
    }

    /**
     * Attach variant values to listing
     */
    private function attachVariants(Listing $listing, array $variants): void
    {
        foreach ($variants as $variantId => $value) {
            if (empty($value)) {
                continue;
            }

            // Check if value is an item ID or custom value
            if (is_array($value)) {
                // Multiple selection (checkbox)
                foreach ($value as $itemId) {
                    $listing->listingVariants()->create([
                        'variant_id' => $variantId,
                        'variant_item_id' => $itemId,
                    ]);
                }
            } elseif (is_numeric($value)) {
                // Single selection (select/radio)
                $listing->listingVariants()->create([
                    'variant_id' => $variantId,
                    'variant_item_id' => $value,
                ]);
            } else {
                // Custom value (text/number/range)
                $listing->listingVariants()->create([
                    'variant_id' => $variantId,
                    'custom_value' => $value,
                ]);
            }
        }
    }

    /**
     * Upload and attach images to listing
     */
    private function uploadImages(Listing $listing, array $images): void
    {
        foreach ($images as $index => $image) {
            $path = $image->store('listings/'.$listing->id, 'public');

            $listing->images()->create([
                'image_path' => $path,
                'original_filename' => $image->getClientOriginalName(),
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }
    }

    /**
     * Upload a single main (primary) image for a listing
     */
    private function uploadMainImage(Listing $listing, \Illuminate\Http\UploadedFile $image): void
    {
        $path = $image->store('listings/'.$listing->id, 'public');

        $listing->images()->create([
            'image_path' => $path,
            'original_filename' => $image->getClientOriginalName(),
            'file_size' => $image->getSize(),
            'mime_type' => $image->getMimeType(),
            'sort_order' => 0,
            'is_primary' => true,
        ]);
    }

    /**
     * Upload detail (non-primary) images for a listing
     */
    private function uploadDetailImages(Listing $listing, array $images): void
    {
        $maxSort = $listing->images()->max('sort_order') ?? 0;

        foreach ($images as $index => $image) {
            $path = $image->store('listings/'.$listing->id, 'public');

            $listing->images()->create([
                'image_path' => $path,
                'original_filename' => $image->getClientOriginalName(),
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'sort_order' => $maxSort + $index + 1,
                'is_primary' => false,
            ]);
        }
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Listing::withTrashed()->where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (! $query->exists()) {
                return $slug;
            }

            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }
    }

    /**
     * Create product variations for a listing
     */
    private function createProductVariations(Listing $listing, array $variations, Request $request): void
    {
        foreach ($variations as $index => $variationData) {
            // Create the variation
            $variation = $listing->variations()->create([
                'sku' => $variationData['sku'],
                'price' => $variationData['price'],
                'discount_price' => $variationData['discount_price'] ?? null,
                'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                'is_default' => ($variationData['is_default'] ?? false) == 1,
                'is_active' => ($variationData['is_active'] ?? true) == 1,
                'sort_order' => $index,
                'manage_stock' => true,
                'allow_backorder' => false,
            ]);

            // Attach variant attributes
            if (! empty($variationData['attributes'])) {
                foreach ($variationData['attributes'] as $variantId => $variantItemId) {
                    if ($variantItemId) {
                        \App\Models\ProductVariationAttribute::create([
                            'product_variation_id' => $variation->id,
                            'variant_id' => $variantId,
                            'variant_item_id' => $variantItemId,
                        ]);
                    }
                }
            }

            // Upload images for this variation
            if ($request->hasFile("variations.{$index}.images")) {
                $images = $request->file("variations.{$index}.images");
                foreach ($images as $imgIndex => $image) {
                    $path = $image->store('variations/'.$variation->id, 'public');

                    \App\Models\ProductVariationImage::create([
                        'product_variation_id' => $variation->id,
                        'image_path' => $path,
                        'original_filename' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'sort_order' => $imgIndex,
                        'is_primary' => $imgIndex === 0,
                    ]);
                }
            }

            // Record initial stock if > 0
            if ($variation->stock_quantity > 0) {
                \App\Models\StockMovement::create([
                    'product_variation_id' => $variation->id,
                    'user_id' => auth()->guard('admin')->id(),
                    'type' => 'initial',
                    'quantity_change' => $variation->stock_quantity,
                    'quantity_before' => 0,
                    'quantity_after' => $variation->stock_quantity,
                    'notes' => 'Initial stock on creation',
                ]);
            }
        }
    }

    /**
     * Display the specified listing
     */
    public function show(Listing $listing)
    {
        $listing->load([
            'user',
            'category',
            'listingType',
            'images',
            'listingVariants.variant',
            'listingVariants.variantItem',
            'variations' => function ($query) {
                $query->with([
                    'attributes.variant',
                    'attributes.variantItem',
                    'images',
                    'stockMovements' => function ($q) {
                        $q->latest('created_at')->limit(5);
                    },
                ])->orderBy('sort_order');
            },
        ]);

        return view('admin.listings.show', compact('listing'));
    }

    /**
     * Show the form for editing the specified listing
     */
    public function edit(Listing $listing)
    {
        $listing->load([
            'images',
            'listingVariants.variant',
            'listingVariants.variantItem',
            'variations' => function ($query) {
                $query->with([
                    'attributes.variant',
                    'attributes.variantItem',
                    'images',
                ])->orderBy('sort_order');
            },
        ]);

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.listings.edit', compact('listing', 'listingTypes'));
    }

    /**
     * Update the specified listing
     */
    public function update(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'listing_type_id' => 'required|exists:listing_types,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:500|unique:listings,slug,'.$listing->id,
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'base_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:base_price',
            'discount_start_date' => 'nullable|date|required_with:discount_price',
            'discount_end_date' => 'nullable|date|after:discount_start_date|required_with:discount_price',
            'currency' => 'nullable|string|max:3',
            'is_negotiable' => 'nullable|boolean',
            'is_visible' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'condition' => 'required|in:new,used',
            'availability_type' => 'nullable|in:in_stock,available_by_order',
            'has_delivery' => 'nullable|boolean',
            'has_domestic_delivery' => 'nullable|boolean',
            'domestic_delivery_price' => 'nullable|numeric|min:0',
            'has_international_delivery' => 'nullable|boolean',
            'international_delivery_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending,active',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'store_name' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',

            // Images
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'detail_images' => 'nullable|array',
            'detail_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:listing_images,id',

            // Variations (for updating existing variations)
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|exists:product_variations,id',
            'variations.*.sku' => 'required|string',
            'variations.*.attributes' => 'nullable|array',
            'variations.*.price' => 'required|numeric|min:0',
            'variations.*.discount_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_default' => 'nullable|boolean',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.images' => 'nullable|array',
            'variations.*.images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // Generate slug if changed
            if ($validated['title'] !== $listing->title || ! empty($validated['slug'])) {
                $slug = ! empty($validated['slug']) ? $validated['slug'] : Str::slug($validated['title']);
                $validated['slug'] = $this->generateUniqueSlug($slug, $listing->id);
            }

            // Set defaults
            $validated['is_negotiable'] = $request->has('is_negotiable');
            $validated['is_visible'] = $request->has('is_visible');
            $validated['is_featured'] = $request->has('is_featured');
            $validated['has_delivery'] = $request->has('has_delivery');
            $validated['has_domestic_delivery'] = $request->has('has_domestic_delivery');
            $validated['has_international_delivery'] = $request->has('has_international_delivery');

            // Update listing
            $listing->update($validated);

            // Handle image deletions
            if (! empty($request->delete_images)) {
                $listing->images()->whereIn('id', $request->delete_images)->delete();
            }

            // Handle new main image
            if ($request->hasFile('main_image')) {
                // Unmark any existing primary image
                $listing->images()->where('is_primary', true)->update(['is_primary' => false]);
                $this->uploadMainImage($listing, $request->file('main_image'));
            }

            // Handle new detail images
            if ($request->hasFile('detail_images')) {
                $this->uploadDetailImages($listing, $request->file('detail_images'));
            }

            // Handle variations update
            if (! empty($request->variations)) {
                $this->updateProductVariations($listing, $request->variations, $request);
            } elseif ($listing->variations()->exists()) {
                // All variations were removed by the user - delete them
                $listing->variations()->delete();
            }

            DB::commit();

            activity()
                ->performedOn($listing)
                ->causedBy(auth()->guard('admin')->user())
                ->log('Listing updated');

            return redirect()
                ->route('admin.listings.show', $listing)
                ->with('success', 'Listing updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Listing update failed', [
                'listing_id' => $listing->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update listing: '.$e->getMessage());
        }
    }

    /**
     * Update product variations for a listing
     */
    private function updateProductVariations(Listing $listing, array $variations, Request $request): void
    {
        $updatedIds = [];

        foreach ($variations as $index => $variationData) {
            if (! empty($variationData['id'])) {
                // Update existing variation
                $variation = $listing->variations()->find($variationData['id']);

                if ($variation) {
                    $variation->update([
                        'sku' => $variationData['sku'],
                        'price' => $variationData['price'],
                        'discount_price' => $variationData['discount_price'] ?? null,
                        'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                        'is_default' => ($variationData['is_default'] ?? false) == 1,
                        'is_active' => ($variationData['is_active'] ?? true) == 1,
                        'sort_order' => $index,
                    ]);

                    $updatedIds[] = $variation->id;

                    // Update attributes if changed
                    if (! empty($variationData['attributes'])) {
                        $variation->attributes()->delete();
                        foreach ($variationData['attributes'] as $variantId => $variantItemId) {
                            if ($variantItemId) {
                                \App\Models\ProductVariationAttribute::create([
                                    'product_variation_id' => $variation->id,
                                    'variant_id' => $variantId,
                                    'variant_item_id' => $variantItemId,
                                ]);
                            }
                        }
                    }

                    // Upload new images if provided
                    if ($request->hasFile("variations.{$index}.images")) {
                        $images = $request->file("variations.{$index}.images");
                        foreach ($images as $imgIndex => $image) {
                            $path = $image->store('variations/'.$variation->id, 'public');

                            \App\Models\ProductVariationImage::create([
                                'product_variation_id' => $variation->id,
                                'image_path' => $path,
                                'original_filename' => $image->getClientOriginalName(),
                                'file_size' => $image->getSize(),
                                'mime_type' => $image->getMimeType(),
                                'sort_order' => $imgIndex,
                                'is_primary' => $imgIndex === 0,
                            ]);
                        }
                    }
                }
            } else {
                // Create new variation (same as createProductVariations)
                $variation = $listing->variations()->create([
                    'sku' => $variationData['sku'],
                    'price' => $variationData['price'],
                    'discount_price' => $variationData['discount_price'] ?? null,
                    'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                    'is_default' => ($variationData['is_default'] ?? false) == 1,
                    'is_active' => ($variationData['is_active'] ?? true) == 1,
                    'sort_order' => $index,
                    'manage_stock' => true,
                    'allow_backorder' => false,
                ]);

                $updatedIds[] = $variation->id;

                // Attach attributes
                if (! empty($variationData['attributes'])) {
                    foreach ($variationData['attributes'] as $variantId => $variantItemId) {
                        if ($variantItemId) {
                            \App\Models\ProductVariationAttribute::create([
                                'product_variation_id' => $variation->id,
                                'variant_id' => $variantId,
                                'variant_item_id' => $variantItemId,
                            ]);
                        }
                    }
                }

                // Upload images
                if ($request->hasFile("variations.{$index}.images")) {
                    $images = $request->file("variations.{$index}.images");
                    foreach ($images as $imgIndex => $image) {
                        $path = $image->store('variations/'.$variation->id, 'public');

                        \App\Models\ProductVariationImage::create([
                            'product_variation_id' => $variation->id,
                            'image_path' => $path,
                            'original_filename' => $image->getClientOriginalName(),
                            'file_size' => $image->getSize(),
                            'mime_type' => $image->getMimeType(),
                            'sort_order' => $imgIndex,
                            'is_primary' => $imgIndex === 0,
                        ]);
                    }
                }

                // Record initial stock
                if ($variation->stock_quantity > 0) {
                    \App\Models\StockMovement::create([
                        'product_variation_id' => $variation->id,
                        'user_id' => auth()->guard('admin')->id(),
                        'type' => 'initial',
                        'quantity_change' => $variation->stock_quantity,
                        'quantity_before' => 0,
                        'quantity_after' => $variation->stock_quantity,
                        'notes' => 'Initial stock on variation creation',
                    ]);
                }
            }
        }

        // Delete variations that were removed
        if (! empty($updatedIds)) {
            $listing->variations()->whereNotIn('id', $updatedIds)->delete();
        }
    }

    /**
     * Update the expiration date for a listing
     */
    public function updateExpiration(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'expires_at' => 'required|date|after:now',
        ]);

        $listing->update(['expires_at' => $validated['expires_at']]);

        activity()
            ->performedOn($listing)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing expiration updated');

        return back()->with('success', 'Listing expiration updated.');
    }

    /**
     * Permanently delete a soft-deleted listing
     */
    public function forceDelete(int $listing_id)
    {
        $listing = Listing::withTrashed()->findOrFail($listing_id);

        $title = $listing->title;
        $listing->forceDelete();

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log("Listing \"{$title}\" permanently deleted");

        return redirect()
            ->route('admin.listings.index', ['status' => 'deleted'])
            ->with('success', "Listing \"{$title}\" has been permanently deleted.");
    }

    /**
     * Restore a soft-deleted listing
     */
    public function restore(int $listing_id)
    {
        $listing = Listing::withTrashed()->findOrFail($listing_id);

        $listing->restore();
        $listing->update(['status' => 'pending']);

        activity()
            ->performedOn($listing)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing restored');

        return redirect()
            ->route('admin.listings.index')
            ->with('success', "Listing \"{$listing->title}\" has been restored.");
    }

    /**
     * Remove the specified listing
     */
    public function destroy(Listing $listing)
    {
        try {
            DB::beginTransaction();

            // Store listing title for message
            $title = $listing->title;

            // Delete the listing (soft delete)
            $listing->delete();

            DB::commit();

            activity()
                ->performedOn($listing)
                ->causedBy(auth()->guard('admin')->user())
                ->log('Listing deleted');

            return redirect()
                ->route('admin.listings.index')
                ->with('success', "Listing \"{$title}\" has been deleted successfully");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Listing deletion failed', [
                'listing_id' => $listing->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.listings.index')
                ->with('error', 'Failed to delete listing: '.$e->getMessage());
        }
    }
}
