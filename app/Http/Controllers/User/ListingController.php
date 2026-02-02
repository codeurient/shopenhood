<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserListingRequest;
use App\Http\Requests\User\UpdateUserListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingType;
use App\Services\ListingService;
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
            $validated['expires_at'] = $this->listingService->calculateExpiresAt();

            $listing = Listing::create($validated);

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
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

    public function edit(Listing $listing)
    {
        $this->authorizeOwnership($listing);

        if ($listing->trashed()) {
            return redirect()->route('user.listings.index')->with('error', 'Cannot edit a deleted listing.');
        }

        $listing->load(['images', 'category']);

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('user.listings.edit', compact('listing', 'categories', 'listingTypes'));
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

            $listing->update($validated);

            // Handle image deletions
            if (! empty($request->delete_images)) {
                $listing->images()->whereIn('id', $request->delete_images)->delete();
            }

            // Handle new images
            if ($request->hasFile('images')) {
                $maxSort = $listing->images()->max('sort_order') ?? -1;
                foreach ($request->file('images') as $index => $image) {
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

    public function forceDestroy(int $listing)
    {
        $listing = Listing::withTrashed()->findOrFail($listing);

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

    public function reshare(int $listing)
    {
        $listing = Listing::withTrashed()->findOrFail($listing);

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
