<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserListingRequest;
use App\Http\Requests\User\UpdateUserListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\ProductVariationAttribute;
use App\Models\ProductVariationImage;
use App\Models\VariationPriceTier;
use App\Services\ListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    public function __construct(private ListingService $listingService) {}

    public function index()
    {
        $user = auth()->user();

        $activeListings = Listing::forUser($user->id)
            ->with(['category', 'listingType', 'primaryImage'])
            ->latest()
            ->get();

        $trashedListings = Listing::onlyTrashed()
            ->forUser($user->id)
            ->with(['category', 'listingType', 'primaryImage'])
            ->latest('deleted_at')
            ->get();

        $retentionDays = $this->listingService->getSoftDeleteRetentionDays();

        return view('user.listings.index', compact(
            'activeListings',
            'trashedListings',
            'retentionDays',
            'user'
        ));
    }

    public function create()
    {
        $user = auth()->user();

        if (! $this->listingService->canUserCreateListing($user)) {
            return redirect()
                ->route('user.listings.index')
                ->with('error', 'You have reached your listing limit. Delete or wait for an existing listing to expire.');
        }

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('user.listings.create', compact('categories', 'listingTypes'));
    }

    public function store(StoreUserListingRequest $request)
    {
        $user = auth()->user();

        if (! $this->listingService->canUserCreateListing($user)) {
            return redirect()
                ->route('user.listings.index')
                ->with('error', 'You have reached your listing limit.');
        }

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            // Generate slug
            $slug = Str::slug($validated['title']);
            $validated['slug'] = $this->generateUniqueSlug($slug);

            // Set system fields
            $validated['user_id'] = $user->id;
            $validated['created_as_role'] = $user->current_role;
            $validated['status'] = 'pending';
            $validated['is_visible'] = true;
            $validated['is_negotiable'] = $request->has('is_negotiable');
            $validated['has_delivery'] = $request->has('has_delivery');
            $validated['has_domestic_delivery'] = $request->has('has_domestic_delivery');
            $validated['has_international_delivery'] = $request->has('has_international_delivery');
            $validated['expires_at'] = $this->listingService->calculateExpiresAt();

            // Store name, wholesale, SEO, availability, and variations only for business users
            if (! $user->isBusinessUser()) {
                unset($validated['store_name']);
                $validated['is_wholesale'] = false;
                unset($validated['wholesale_min_order_qty']);
                unset($validated['wholesale_qty_increment']);
                unset($validated['wholesale_lead_time_days']);
                unset($validated['wholesale_sample_available']);
                unset($validated['wholesale_sample_price']);
                unset($validated['wholesale_terms']);
                unset($validated['meta_title']);
                unset($validated['meta_description']);
                // Availability, variants/variations are business-only features
                unset($validated['availability_type']);
                unset($validated['variants']);
                unset($validated['variations']);
            } else {
                $validated['is_wholesale'] = $request->has('is_wholesale');
                $validated['wholesale_sample_available'] = $request->has('wholesale_sample_available');
            }

            // Extract variant/variation data before model create (only for business users)
            $variantsData = $user->isBusinessUser() ? ($validated['variants'] ?? []) : [];
            $variationsData = $user->isBusinessUser() ? ($validated['variations'] ?? []) : [];
            unset($validated['main_image'], $validated['detail_images'], $validated['variants'], $validated['variations']);

            $listing = Listing::create($validated);

            // Handle main image
            if ($request->hasFile('main_image')) {
                $image = $request->file('main_image');
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

            // Handle detail images
            if ($request->hasFile('detail_images')) {
                foreach ($request->file('detail_images') as $index => $image) {
                    $path = $image->store('listings/'.$listing->id, 'public');
                    $listing->images()->create([
                        'image_path' => $path,
                        'original_filename' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'sort_order' => $index + 1,
                        'is_primary' => false,
                    ]);
                }
            }

            // Attach listing variants (sidebar selections)
            if (! empty($variantsData)) {
                $this->attachVariants($listing, $variantsData);
            }

            // Create product variations (SKU table)
            if (! empty($variationsData)) {
                $this->createProductVariations($listing, $variationsData, $request);
            }

            DB::commit();

            return redirect()
                ->route('user.listings.index')
                ->with('success', 'Your listing has been submitted for review.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User listing creation failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Failed to create listing. Please try again.');
        }
    }

    public function show(Listing $listing)
    {
        $this->authorizeOwnership($listing);

        $listing->load([
            'category',
            'listingType',
            'images',
            'listingVariants.variant',
            'listingVariants.variantItem',
            'variations.attributes.variant',
            'variations.attributes.variantItem',
            'variations.images',
        ]);

        return view('user.listings.show', compact('listing'));
    }

    public function edit(Listing $listing)
    {
        $this->authorizeOwnership($listing);

        if ($listing->trashed()) {
            return redirect()->route('user.listings.index')->with('error', 'Cannot edit a deleted listing.');
        }

        $listing->load([
            'images',
            'category',
            'listingVariants.variant',
            'listingVariants.variantItem',
            'variations.attributes.variant',
            'variations.attributes.variantItem',
            'variations.images',
        ]);

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $categoryChain = $this->getCategoryChain($listing->category);

        return view('user.listings.edit', compact('listing', 'categories', 'listingTypes', 'categoryChain'));
    }

    public function update(UpdateUserListingRequest $request, Listing $listing)
    {
        $this->authorizeOwnership($listing);

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            // Regenerate slug if title changed
            if ($validated['title'] !== $listing->title) {
                $validated['slug'] = $this->generateUniqueSlug(Str::slug($validated['title']), $listing->id);
            }

            // Reset to pending if content changed
            $validated['status'] = 'pending';
            $validated['is_negotiable'] = $request->has('is_negotiable');
            $validated['has_delivery'] = $request->has('has_delivery');
            $validated['has_domestic_delivery'] = $request->has('has_domestic_delivery');
            $validated['has_international_delivery'] = $request->has('has_international_delivery');

            // Store name, wholesale, SEO, availability, and variations only for business users
            $user = auth()->user();
            if (! $user->isBusinessUser()) {
                unset($validated['store_name']);
                $validated['is_wholesale'] = false;
                unset($validated['wholesale_min_order_qty']);
                unset($validated['wholesale_qty_increment']);
                unset($validated['wholesale_lead_time_days']);
                unset($validated['wholesale_sample_available']);
                unset($validated['wholesale_sample_price']);
                unset($validated['wholesale_terms']);
                unset($validated['meta_title']);
                unset($validated['meta_description']);
                // Availability, variants/variations are business-only features
                unset($validated['availability_type']);
                unset($validated['variants']);
                unset($validated['variations']);
            } else {
                $validated['is_wholesale'] = $request->has('is_wholesale');
                $validated['wholesale_sample_available'] = $request->has('wholesale_sample_available');
            }

            // Extract variant/variation data before model update (only for business users)
            $variantsData = $user->isBusinessUser() ? ($validated['variants'] ?? []) : [];
            $variationsData = $user->isBusinessUser() ? ($validated['variations'] ?? []) : [];
            unset($validated['main_image'], $validated['detail_images'], $validated['delete_images'], $validated['variants'], $validated['variations']);

            $listing->update($validated);

            // Handle image deletions
            if (! empty($request->delete_images)) {
                $listing->images()->whereIn('id', $request->delete_images)->delete();
            }

            // Handle variation image deletions
            if (! empty($request->delete_variation_image_ids)) {
                ProductVariationImage::whereIn('id', $request->delete_variation_image_ids)
                    ->whereHas('productVariation', fn ($q) => $q->where('listing_id', $listing->id))
                    ->delete();
            }

            // Handle main image replacement
            if ($request->hasFile('main_image')) {
                // Mark old primary as non-primary
                $listing->images()->where('is_primary', true)->update(['is_primary' => false]);

                $image = $request->file('main_image');
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

            // Handle new detail images
            if ($request->hasFile('detail_images')) {
                $maxSort = $listing->images()->max('sort_order') ?? 0;
                foreach ($request->file('detail_images') as $index => $image) {
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

            // Update listing variants (sidebar selections)
            $listing->listingVariants()->delete();
            if (! empty($variantsData)) {
                $this->attachVariants($listing, $variantsData);
            }

            // Update product variations (SKU table)
            if (! empty($variationsData)) {
                $this->updateProductVariations($listing, $variationsData, $request);
            } else {
                // If no variations submitted, remove all existing
                $listing->variations()->delete();
            }

            DB::commit();

            return redirect()
                ->route('user.listings.index')
                ->with('success', 'Listing updated and resubmitted for review.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User listing update failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Failed to update listing.');
        }
    }

    public function toggleVisibility(Listing $listing)
    {
        $this->authorizeOwnership($listing);

        $this->listingService->toggleListingVisibility(auth()->user(), $listing);

        $status = $listing->fresh()->is_visible ? 'visible' : 'hidden';

        return back()->with('success', "Listing is now {$status}.");
    }

    public function destroy(Listing $listing)
    {
        $this->authorizeOwnership($listing);

        $this->listingService->softDeleteListing(auth()->user(), $listing);

        return redirect()
            ->route('user.listings.index')
            ->with('success', 'Listing deleted. You can reshare it within the retention period.');
    }

    public function forceDestroy(int $listing_id)
    {
        $listing = Listing::withTrashed()->findOrFail($listing_id);

        $this->authorizeOwnership($listing);

        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return back()->with('error', 'Only business users can permanently delete listings.');
        }

        if (! $listing->trashed()) {
            return back()->with('error', 'Only trashed listings can be permanently deleted.');
        }

        $this->listingService->forceDeleteListing($user, $listing);

        return redirect()
            ->route('user.listings.index')
            ->with('success', 'Listing permanently deleted.');
    }

    public function reshare(int $listing_id)
    {
        $listing = Listing::withTrashed()->findOrFail($listing_id);

        $this->authorizeOwnership($listing);

        $user = auth()->user();

        if (! $this->listingService->canReshareListing($user, $listing)) {
            return back()->with('error', 'You cannot reshare this listing. Check your listing limit.');
        }

        $this->listingService->reshareListing($user, $listing);

        return redirect()
            ->route('user.listings.index')
            ->with('success', 'Listing reshared and submitted for review.');
    }

    private function authorizeOwnership(Listing $listing): void
    {
        if (! $listing->belongsToUser(auth()->id())) {
            abort(403, 'This listing does not belong to you.');
        }
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function getCategoryChain(?Category $category): array
    {
        $chain = [];

        while ($category) {
            array_unshift($chain, [
                'id' => $category->id,
                'name' => $category->name,
            ]);
            $category = $category->parent;
        }

        return $chain;
    }

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

    private function attachVariants(Listing $listing, array $variants): void
    {
        foreach ($variants as $variantId => $value) {
            if (empty($value)) {
                continue;
            }

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

    private function createProductVariations(Listing $listing, array $variations, Request $request): void
    {
        foreach ($variations as $index => $variationData) {
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
                        ProductVariationAttribute::create([
                            'product_variation_id' => $variation->id,
                            'variant_id' => $variantId,
                            'variant_item_id' => $variantItemId,
                        ]);
                    }
                }
            }

            // Upload variation images
            if ($request->hasFile("variations.{$index}.images")) {
                $images = $request->file("variations.{$index}.images");
                foreach ($images as $imgIndex => $image) {
                    $path = $image->store('variations/'.$variation->id, 'public');

                    ProductVariationImage::create([
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

            // Create price tiers
            if (! empty($variationData['price_tiers'])) {
                $this->syncPriceTiers($variation, $variationData['price_tiers']);
            }
        }
    }

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

                    // Update attributes
                    if (! empty($variationData['attributes'])) {
                        $variation->attributes()->delete();
                        foreach ($variationData['attributes'] as $variantId => $variantItemId) {
                            if ($variantItemId) {
                                ProductVariationAttribute::create([
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

                            ProductVariationImage::create([
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

                    // Sync price tiers
                    $this->syncPriceTiers($variation, $variationData['price_tiers'] ?? []);
                }
            } else {
                // Create new variation
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
                            ProductVariationAttribute::create([
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

                        ProductVariationImage::create([
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

                // Create price tiers
                if (! empty($variationData['price_tiers'])) {
                    $this->syncPriceTiers($variation, $variationData['price_tiers']);
                }
            }
        }

        // Delete variations that were removed
        if (! empty($updatedIds)) {
            $listing->variations()->whereNotIn('id', $updatedIds)->delete();
        }
    }

    private function syncPriceTiers($variation, array $tiers): void
    {
        $variation->priceTiers()->delete();

        foreach ($tiers as $tier) {
            if (empty($tier['min_quantity']) || empty($tier['unit_price'])) {
                continue;
            }

            VariationPriceTier::create([
                'product_variation_id' => $variation->id,
                'min_quantity' => $tier['min_quantity'],
                'max_quantity' => $tier['max_quantity'] ?? null,
                'unit_price' => $tier['unit_price'],
            ]);
        }
    }
}
