<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use App\Models\VariantItem;
use Illuminate\Http\Request;

class VariantItemController extends Controller
{
    /**
     * Display variant items for a specific variant
     */
    public function index(Request $request, Variant $variant)
    {
        $items = $variant->items()
            ->orderBy('sort_order')
            ->orderBy('value')
            ->paginate(50)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.variants.items.partials.table', compact('items', 'variant'))->render(),
                'pagination' => $items->links()->render(),
            ]);
        }

        return view('admin.variants.items.index', compact('variant', 'items'));
    }

    /**
     * Show form to create variant item
     */
    public function create(Variant $variant)
    {
        return view('admin.variants.items.create', compact('variant'));
    }

    /**
     * Store variant item
     */
    public function store(Request $request, Variant $variant)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'display_value' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Check for duplicate value
        $exists = $variant->items()
            ->where('value', $validated['value'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['value' => 'This variant item already exists'])
                ->withInput();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('variant-items', 'public');
        }

        $validated['variant_id'] = $variant->id;
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $item = VariantItem::create($validated);

        activity()
            ->performedOn($item)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['variant' => $variant->name])
            ->log('Variant item created');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Variant item added successfully',
                'item' => $item,
            ]);
        }

        return redirect()
            ->route('admin.variants.items.index', $variant)
            ->with('success', 'Variant item added successfully');
    }
}