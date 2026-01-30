<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingTypeController extends Controller
{
    /**
     * Display a listing of listing types
     */
    public function index(Request $request)
    {
        $query = ListingType::withCount('listings');

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $listingTypes = $query->ordered()->paginate(20);

        // Statistics
        $stats = [
            'total' => ListingType::count(),
            'active' => ListingType::where('is_active', true)->count(),
            'inactive' => ListingType::where('is_active', false)->count(),
        ];

        return view('admin.listing-types.index', compact('listingTypes', 'stats'));
    }

    /**
     * Show the form for creating a new listing type
     */
    public function create()
    {
        return view('admin.listing-types.create');
    }

    /**
     * Store a newly created listing type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:listing_types,name',
            'slug' => 'nullable|string|max:100|unique:listing_types,slug',
            'description' => 'nullable|string',
            'requires_price' => 'nullable|boolean',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set defaults
        $validated['requires_price'] = $request->has('requires_price');
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $listingType = ListingType::create($validated);

        activity()
            ->performedOn($listingType)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing type created');

        return redirect()
            ->route('admin.listing-types.index')
            ->with('success', 'Listing type created successfully');
    }

    /**
     * Display the specified listing type
     */
    public function show(ListingType $listingType)
    {
        $listingType->loadCount('listings');

        return view('admin.listing-types.show', compact('listingType'));
    }

    /**
     * Show the form for editing the specified listing type
     */
    public function edit(ListingType $listingType)
    {
        return view('admin.listing-types.edit', compact('listingType'));
    }

    /**
     * Update the specified listing type
     */
    public function update(Request $request, ListingType $listingType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:listing_types,name,'.$listingType->id,
            'slug' => 'nullable|string|max:100|unique:listing_types,slug,'.$listingType->id,
            'description' => 'nullable|string',
            'requires_price' => 'nullable|boolean',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set defaults
        $validated['requires_price'] = $request->has('requires_price');
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $listingType->update($validated);

        activity()
            ->performedOn($listingType)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing type updated');

        return redirect()
            ->route('admin.listing-types.index')
            ->with('success', 'Listing type updated successfully');
    }

    /**
     * Remove the specified listing type
     */
    public function destroy(ListingType $listingType)
    {
        // Check if listing type has listings
        if ($listingType->listings()->count() > 0) {
            return redirect()
                ->route('admin.listing-types.index')
                ->with('error', 'Cannot delete listing type that has listings associated with it');
        }

        $listingType->delete();

        activity()
            ->performedOn($listingType)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing type deleted');

        return redirect()
            ->route('admin.listing-types.index')
            ->with('success', 'Listing type deleted successfully');
    }

    /**
     * Toggle listing type status
     */
    public function toggleStatus(ListingType $listingType)
    {
        $listingType->update([
            'is_active' => ! $listingType->is_active,
        ]);

        activity()
            ->performedOn($listingType)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing type status toggled');

        return redirect()
            ->back()
            ->with('success', 'Listing type status updated successfully');
    }

    /**
     * Bulk delete listing types
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:listing_types,id',
        ]);

        $count = 0;
        foreach ($validated['ids'] as $id) {
            $listingType = ListingType::find($id);

            // Skip if has listings
            if ($listingType && $listingType->listings()->count() === 0) {
                $listingType->delete();
                $count++;
            }
        }

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log("Bulk deleted {$count} listing types");

        return redirect()
            ->route('admin.listing-types.index')
            ->with('success', "Successfully deleted {$count} listing type(s)");
    }

    /**
     * Reorder listing types
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:listing_types,id',
            'order.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['order'] as $item) {
            ListingType::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing types reordered');

        return response()->json([
            'success' => true,
            'message' => 'Listing types reordered successfully',
        ]);
    }
}
