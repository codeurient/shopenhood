<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VariantController extends Controller
{
    /**
     * Display a listing of variants with pagination and search
     */
    public function index(Request $request)
    {
        $query = Variant::withCount('items');

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $variants = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.variants.partials.table', compact('variants'))->render(),
                'pagination' => $variants->links()->render(),
            ]);
        }

        return view('admin.variants.index', compact('variants'));
    }

    /**
     * Show the form for creating a new variant
     */
    public function create()
    {
        return view('admin.variants.create');
    }

    /**
     * Store a newly created variant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:variants,name',
            'slug' => 'nullable|string|max:255|unique:variants,slug',
            'type' => 'required|in:select,radio,checkbox,text,number,range',
            'is_required' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Auto-generate slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure unique slug
        $validated['slug'] = $this->generateUniqueSlug($validated['slug']);

        // Set defaults
        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $variant = Variant::create($validated);

        activity()
            ->performedOn($variant)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Variant created');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Variant created successfully',
                'variant' => $variant,
                'redirect' => route('admin.variants.index'),
            ]);
        }

        return redirect()
            ->route('admin.variants.index')
            ->with('success', 'Variant created successfully');
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Variant::where('slug', $slug);
            
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
    }
}