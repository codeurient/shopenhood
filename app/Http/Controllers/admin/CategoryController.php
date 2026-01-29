<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure slug uniqueness
        $validated['slug'] = $this->generateUniqueSlug($validated['slug']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Calculate level and path
        if (! empty($validated['parent_id'])) {
            $parent = Category::findOrFail($validated['parent_id']);
            $validated['level'] = $parent->level + 1; // e.g., 2 if parent is level 1
            $validated['path'] = $parent->path ? $parent->path.'/'.$parent->id : (string) $parent->id; // e.g., "1/4/7"
        } else {
            $validated['level'] = 1;
            $validated['path'] = null;
        }

        // Set default values
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Create category
        $category = Category::create($validated);

        activity()
            ->performedOn($category)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => $validated])
            ->log('Category created');

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Generate a unique slug for the category.
     */
    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Category::where('slug', $slug);

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
     * Get categories for AJAX requests (used by create form).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChildren(Request $request, ?int $categoryId = null)
    {
        $query = Category::query()
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->where('is_active', true)
            ->orderBy('sort_order');

        if ($categoryId) {
            $query->where('parent_id', $categoryId);
        } else {
            $query->whereNull('parent_id');
        }

        $categories = $query->get()->map(function ($category) {
            return $this->formatCategoryForSelect($category);
        });

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    private function formatCategoryForSelect(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'level' => $category->level,
            'children' => $category->children->map(function ($child) {
                return $this->formatCategoryForSelect($child);
            })->toArray(),
        ];
    }
}
