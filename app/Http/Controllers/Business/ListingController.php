<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\StoreBusinessListingRequest;
use App\Http\Requests\Business\UpdateBusinessListingRequest;
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
        $retentionDays = $this->listingService->getSoftDeleteRetentionDays();

        if (! $user->isBusinessUser()) {
            return view('business.listings.index', [
                'activeListings' => collect(),
                'trashedListings' => collect(),
                'retentionDays' => $retentionDays,
                'user' => $user,
            ]);
        }

        $activeListings = Listing::businessMode()
            ->forUser($user->id)
            ->with(['category', 'listingType', 'variations'])
            ->latest()
            ->get();

        $trashedListings = Listing::onlyTrashed()
            ->businessMode()
            ->forUser($user->id)
            ->with(['category', 'listingType'])
            ->latest('deleted_at')
            ->get();

        return view('business.listings.index', compact(
            'activeListings',
            'trashedListings',
            'retentionDays',
            'user'
        ));
    }

    public function create()
    {
        $user = auth()->user();

        if (! $this->canCreateListing($user)) {
            return redirect()
                ->route('business.listings.index')
                ->with('error', 'You have reached your listing limit.');
        }

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('business.listings.create', compact('categories', 'listingTypes'));
    }

    public function store(StoreBusinessListingRequest $request)
    {
        $user = auth()->user();

        if (! $this->canCreateListing($user)) {
            return redirect()
                ->route('business.listings.index')
                ->with('error', 'You have reached your listing limit.');
        }

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $slug = Str::slug($validated['title']);
            $validated['slug'] = $this->generateUniqueSlug($slug);

            $validated['user_id'] = $user->id;
            $validated['listing_mode'] = 'business';
            $validated['created_as_role'] = $user->current_role;
            $validated['status'] = 'pending';
            $validated['is_visible'] = true;
            $validated['is_wholesale'] = false;
            $validated['has_delivery'] = $request->has('has_delivery');
            $validated['has_domestic_delivery'] = $request->has('has_domestic_delivery');
            $validated['has_international_delivery'] = $request->has('has_international_delivery');
            $validated['expires_at'] = $this->listingService->calculateExpiresAt();

            $variantsData = $validated['variants'] ?? [];
            $variationsData = $validated['variations'] ?? [];
            unset($validated['variants'], $validated['variations']);

            $listing = Listing::create($validated);

            if (! empty($variantsData)) {
                $this->attachVariants($listing, $variantsData);
            }

            if (! empty($variationsData)) {
                $this->createProductVariations($listing, $variationsData, $request);
            }

            DB::commit();

            return redirect()
                ->route('business.listings.index')
                ->with('success', 'Your listing has been submitted for review.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Business listing creation failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Failed to create listing. Please try again.');
        }
    }

    public function edit(Listing $listing)
    {
        $this->authorizeOwnership($listing);
        $this->authorizeBusinessMode($listing);

        if ($listing->trashed()) {
            return redirect()->route('business.listings.index')->with('error', 'Cannot edit a deleted listing.');
        }

        $listing->load([
            'category',
            'listingVariants.variant',
            'listingVariants.variantItem',
            'variations.attributes.variant',
            'variations.attributes.variantItem',
            'variations.images',
            'variations.priceTiers',
        ]);

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $categoryChain = $this->getCategoryChain($listing->category);

        return view('business.listings.edit', compact('listing', 'categories', 'listingTypes', 'categoryChain'));
    }

    public function update(UpdateBusinessListingRequest $request, Listing $listing)
    {
        $this->authorizeOwnership($listing);
        $this->authorizeBusinessMode($listing);

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            if ($validated['title'] !== $listing->title) {
                $validated['slug'] = $this->generateUniqueSlug(Str::slug($validated['title']), $listing->id);
            }

            $validated['status'] = 'pending';
            $validated['is_wholesale'] = false;
            $validated['has_delivery'] = $request->has('has_delivery');
            $validated['has_domestic_delivery'] = $request->has('has_domestic_delivery');
            $validated['has_international_delivery'] = $request->has('has_international_delivery');

            $variantsData = $validated['variants'] ?? [];
            $variationsData = $validated['variations'] ?? [];
            unset($validated['variants'], $validated['variations'], $validated['delete_variation_image_ids']);

            $listing->update($validated);

            // Handle variation image deletions
            if (! empty($request->delete_variation_image_ids)) {
                ProductVariationImage::whereIn('id', $request->delete_variation_image_ids)
                    ->whereHas('productVariation', fn ($q) => $q->where('listing_id', $listing->id))
                    ->delete();
            }

            $listing->listingVariants()->delete();
            if (! empty($variantsData)) {
                $this->attachVariants($listing, $variantsData);
            }

            if (! empty($variationsData)) {
                $this->updateProductVariations($listing, $variationsData, $request);
            } else {
                $listing->variations()->delete();
            }

            DB::commit();

            return redirect()
                ->route('business.listings.index')
                ->with('success', 'Listing updated and resubmitted for review.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Business listing update failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Failed to update listing.');
        }
    }

    public function toggleVisibility(Listing $listing)
    {
        $this->authorizeOwnership($listing);
        $this->authorizeBusinessMode($listing);

        $this->listingService->toggleListingVisibility(auth()->user(), $listing);

        $status = $listing->fresh()->is_visible ? 'visible' : 'hidden';

        return back()->with('success', "Listing is now {$status}.");
    }

    public function destroy(Listing $listing)
    {
        $this->authorizeOwnership($listing);
        $this->authorizeBusinessMode($listing);

        $this->listingService->softDeleteListing(auth()->user(), $listing);

        return redirect()
            ->route('business.listings.index')
            ->with('success', 'Listing deleted. You can reshare it within the retention period.');
    }

    public function forceDestroy(int $listing_id)
    {
        $listing = Listing::withTrashed()->findOrFail($listing_id);

        $this->authorizeOwnership($listing);
        $this->authorizeBusinessMode($listing);

        if (! $listing->trashed()) {
            return back()->with('error', 'Only trashed listings can be permanently deleted.');
        }

        $this->listingService->forceDeleteListing(auth()->user(), $listing);

        return redirect()
            ->route('business.listings.index')
            ->with('success', 'Listing permanently deleted.');
    }

    public function reshare(int $listing_id)
    {
        $listing = Listing::withTrashed()->findOrFail($listing_id);

        $this->authorizeOwnership($listing);
        $this->authorizeBusinessMode($listing);

        if (! $this->canReshare(auth()->user(), $listing)) {
            return back()->with('error', 'You cannot reshare this listing. Check your listing limit.');
        }

        $this->listingService->reshareListing(auth()->user(), $listing);

        return redirect()
            ->route('business.listings.index')
            ->with('success', 'Listing reshared and submitted for review.');
    }

    private function canCreateListing(\App\Models\User $user): bool
    {
        $limit = $user->listing_limit;

        if ($limit === null) {
            return true;
        }

        $currentCount = Listing::businessMode()->forUser($user->id)->count();

        return $currentCount < $limit;
    }

    private function canReshare(\App\Models\User $user, Listing $listing): bool
    {
        if (! $listing->trashed()) {
            return false;
        }

        if (! $listing->belongsToUser($user->id)) {
            return false;
        }

        return $this->canCreateListing($user);
    }

    private function authorizeOwnership(Listing $listing): void
    {
        if (! $listing->belongsToUser(auth()->id())) {
            abort(403, 'This listing does not belong to you.');
        }
    }

    private function authorizeBusinessMode(Listing $listing): void
    {
        if ($listing->listing_mode !== 'business') {
            abort(403, 'This listing is not a business listing.');
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
                foreach ($value as $itemId) {
                    $listing->listingVariants()->create([
                        'variant_id' => $variantId,
                        'variant_item_id' => $itemId,
                    ]);
                }
            } elseif (is_numeric($value)) {
                $listing->listingVariants()->create([
                    'variant_id' => $variantId,
                    'variant_item_id' => $value,
                ]);
            } else {
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
                'discount_start_date' => $variationData['discount_start_date'] ?? null,
                'discount_end_date' => $variationData['discount_end_date'] ?? null,
                'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                'is_default' => ($variationData['is_default'] ?? false) == 1,
                'is_active' => ($variationData['is_active'] ?? true) == 1,
                'sort_order' => $index,
                'manage_stock' => true,
                'allow_backorder' => false,
                'is_wholesale' => ($variationData['is_wholesale'] ?? false) == 1,
                'wholesale_min_order_qty' => $variationData['wholesale_min_order_qty'] ?? null,
                'wholesale_qty_increment' => $variationData['wholesale_qty_increment'] ?? 1,
                'wholesale_lead_time_days' => $variationData['wholesale_lead_time_days'] ?? null,
                'wholesale_sample_available' => ($variationData['wholesale_sample_available'] ?? false) == 1,
                'wholesale_sample_price' => $variationData['wholesale_sample_price'] ?? null,
                'wholesale_terms' => $variationData['wholesale_terms'] ?? null,
            ]);

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

            if ($request->hasFile("variations.{$index}.images")) {
                foreach ($request->file("variations.{$index}.images") as $imgIndex => $image) {
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
                $variation = $listing->variations()->find($variationData['id']);

                if ($variation) {
                    $variation->update([
                        'sku' => $variationData['sku'],
                        'price' => $variationData['price'],
                        'discount_price' => $variationData['discount_price'] ?? null,
                        'discount_start_date' => $variationData['discount_start_date'] ?? null,
                        'discount_end_date' => $variationData['discount_end_date'] ?? null,
                        'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                        'is_default' => ($variationData['is_default'] ?? false) == 1,
                        'is_active' => ($variationData['is_active'] ?? true) == 1,
                        'sort_order' => $index,
                        'is_wholesale' => ($variationData['is_wholesale'] ?? false) == 1,
                        'wholesale_min_order_qty' => $variationData['wholesale_min_order_qty'] ?? null,
                        'wholesale_qty_increment' => $variationData['wholesale_qty_increment'] ?? 1,
                        'wholesale_lead_time_days' => $variationData['wholesale_lead_time_days'] ?? null,
                        'wholesale_sample_available' => ($variationData['wholesale_sample_available'] ?? false) == 1,
                        'wholesale_sample_price' => $variationData['wholesale_sample_price'] ?? null,
                        'wholesale_terms' => $variationData['wholesale_terms'] ?? null,
                    ]);

                    $updatedIds[] = $variation->id;

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

                    if ($request->hasFile("variations.{$index}.images")) {
                        foreach ($request->file("variations.{$index}.images") as $imgIndex => $image) {
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

                    $this->syncPriceTiers($variation, $variationData['price_tiers'] ?? []);
                }
            } else {
                $variation = $listing->variations()->create([
                    'sku' => $variationData['sku'],
                    'price' => $variationData['price'],
                    'discount_price' => $variationData['discount_price'] ?? null,
                    'discount_start_date' => $variationData['discount_start_date'] ?? null,
                    'discount_end_date' => $variationData['discount_end_date'] ?? null,
                    'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                    'is_default' => ($variationData['is_default'] ?? false) == 1,
                    'is_active' => ($variationData['is_active'] ?? true) == 1,
                    'sort_order' => $index,
                    'manage_stock' => true,
                    'allow_backorder' => false,
                    'is_wholesale' => ($variationData['is_wholesale'] ?? false) == 1,
                    'wholesale_min_order_qty' => $variationData['wholesale_min_order_qty'] ?? null,
                    'wholesale_qty_increment' => $variationData['wholesale_qty_increment'] ?? 1,
                    'wholesale_lead_time_days' => $variationData['wholesale_lead_time_days'] ?? null,
                    'wholesale_sample_available' => ($variationData['wholesale_sample_available'] ?? false) == 1,
                    'wholesale_sample_price' => $variationData['wholesale_sample_price'] ?? null,
                    'wholesale_terms' => $variationData['wholesale_terms'] ?? null,
                ]);

                $updatedIds[] = $variation->id;

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

                if ($request->hasFile("variations.{$index}.images")) {
                    foreach ($request->file("variations.{$index}.images") as $imgIndex => $image) {
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

                if (! empty($variationData['price_tiers'])) {
                    $this->syncPriceTiers($variation, $variationData['price_tiers']);
                }
            }
        }

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
