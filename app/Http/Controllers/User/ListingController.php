<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserListingRequest;
use App\Http\Requests\User\UpdateUserListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
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

        $activeListings = Listing::normalMode()
            ->forUser($user->id)
            ->with(['category', 'listingType', 'primaryImage'])
            ->latest()
            ->get();

        $trashedListings = Listing::onlyTrashed()
            ->normalMode()
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

        if (! $this->listingService->canUserCreateNormalListing($user)) {
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

        if (! $this->listingService->canUserCreateNormalListing($user)) {
            return redirect()
                ->route('user.listings.index')
                ->with('error', 'You have reached your listing limit.');
        }

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $slug = Str::slug($validated['title']);
            $validated['slug'] = $this->generateUniqueSlug($slug);

            $validated['user_id'] = $user->id;
            $validated['listing_mode'] = 'normal';
            $validated['created_as_role'] = $user->current_role;
            $validated['status'] = 'pending';
            $validated['is_visible'] = true;
            $validated['is_wholesale'] = false;
            $validated['is_negotiable'] = $request->has('is_negotiable');
            $validated['has_delivery'] = $request->has('has_delivery');
            $validated['has_domestic_delivery'] = $request->has('has_domestic_delivery');
            $validated['has_international_delivery'] = $request->has('has_international_delivery');
            $validated['expires_at'] = $this->listingService->calculateExpiresAt();

            // Normal users never submit business-only fields
            unset(
                $validated['store_name'],
                $validated['wholesale_min_order_qty'],
                $validated['wholesale_qty_increment'],
                $validated['wholesale_lead_time_days'],
                $validated['wholesale_sample_available'],
                $validated['wholesale_sample_price'],
                $validated['wholesale_terms'],
                $validated['meta_title'],
                $validated['meta_description'],
                $validated['availability_type'],
                $validated['variants'],
                $validated['variations'],
                $validated['variant_attributes']
            );

            unset($validated['main_image'], $validated['detail_images'], $validated['product_images']);

            $listing = Listing::create($validated);

            // Handle listing-level images (first image = primary)
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $index => $image) {
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
            } elseif ($request->hasFile('main_image')) {
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

            if ($validated['title'] !== $listing->title) {
                $validated['slug'] = $this->generateUniqueSlug(Str::slug($validated['title']), $listing->id);
            }

            $validated['status'] = 'pending';
            $validated['is_wholesale'] = false;
            $validated['is_negotiable'] = $request->has('is_negotiable');
            $validated['has_delivery'] = $request->has('has_delivery');
            $validated['has_domestic_delivery'] = $request->has('has_domestic_delivery');
            $validated['has_international_delivery'] = $request->has('has_international_delivery');

            unset(
                $validated['store_name'],
                $validated['wholesale_min_order_qty'],
                $validated['wholesale_qty_increment'],
                $validated['wholesale_lead_time_days'],
                $validated['wholesale_sample_available'],
                $validated['wholesale_sample_price'],
                $validated['wholesale_terms'],
                $validated['meta_title'],
                $validated['meta_description'],
                $validated['availability_type'],
                $validated['variants'],
                $validated['variations'],
                $validated['variant_attributes']
            );

            unset($validated['main_image'], $validated['detail_images'], $validated['product_images'], $validated['delete_images']);

            $listing->update($validated);

            // Handle image deletions (model-level so deleting events fire and files are cleaned up)
            if (! empty($request->delete_images)) {
                $listing->images()->whereIn('id', $request->delete_images)->get()->each->delete();

                if (! $listing->images()->where('is_primary', true)->exists()) {
                    $listing->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
                }
            }

            // Handle image uploads
            if ($request->hasFile('product_images')) {
                $listing->images()->where('is_primary', true)->update(['is_primary' => false]);
                $maxSort = $listing->images()->max('sort_order') ?? -1;
                $hasPrimary = $listing->images()->where('is_primary', true)->exists();

                foreach ($request->file('product_images') as $index => $image) {
                    $path = $image->store('listings/'.$listing->id, 'public');
                    $listing->images()->create([
                        'image_path' => $path,
                        'original_filename' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'sort_order' => $maxSort + $index + 1,
                        'is_primary' => $index === 0 && ! $hasPrimary,
                    ]);
                }
            } elseif ($request->hasFile('main_image')) {
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

        if (! $listing->trashed()) {
            return back()->with('error', 'Only trashed listings can be permanently deleted.');
        }

        $this->listingService->forceDeleteListing(auth()->user(), $listing);

        return redirect()
            ->route('user.listings.index')
            ->with('success', 'Listing permanently deleted.');
    }

    public function bulkForceDestroyTrashed(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No listings selected.');
        }

        $user = auth()->user();

        Listing::onlyTrashed()
            ->normalMode()
            ->forUser($user->id)
            ->whereIn('id', $ids)
            ->get()
            ->each(fn ($listing) => $this->listingService->forceDeleteListing($user, $listing));

        return redirect()
            ->route('user.listings.index')
            ->with('success', 'Selected listings permanently deleted.');
    }

    public function forceDestroyAllTrashed()
    {
        $user = auth()->user();

        Listing::onlyTrashed()
            ->normalMode()
            ->forUser($user->id)
            ->get()
            ->each(fn ($listing) => $this->listingService->forceDeleteListing($user, $listing));

        return redirect()
            ->route('user.listings.index')
            ->with('success', 'All deleted listings permanently removed.');
    }

    public function reshare(int $listing_id)
    {
        $listing = Listing::withTrashed()->findOrFail($listing_id);

        $this->authorizeOwnership($listing);

        if (! $this->listingService->canReshareNormalListing(auth()->user(), $listing)) {
            return back()->with('error', 'You cannot reshare this listing. Check your listing limit.');
        }

        $this->listingService->reshareListing(auth()->user(), $listing);

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
}
