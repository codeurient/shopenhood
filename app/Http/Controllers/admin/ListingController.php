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
        $query = Listing::with(['user', 'category', 'listingType', 'images'])
            ->withCount(['images', 'variants', 'variations']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('slug', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
            'availability_type' => 'nullable|in:in_stock,available_by_order',
            'status' => 'required|in:draft,pending,active',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'store_name' => 'nullable|string|max:255',
            'created_as_role' => 'nullable|in:admin,normal_user,business_user',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',

            // Variant values
            'variants' => 'nullable|array',
            'variants.*' => 'nullable',

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
    public function getCategoryVariants(Category $category)
    {
        try {
            // Get variants assigned only to this specific category (not hierarchy)
            $variants = Variant::whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
                ->with(['items' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
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
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'type' => $variant->type,
                        'is_required' => $variant->pivot->is_required ?? false,
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
     * Generate unique slug
     */
    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Listing::where('slug', $slug);

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
}
