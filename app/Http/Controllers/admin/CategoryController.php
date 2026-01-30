<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with statistics
     */
    public function index(Request $request)
    {
        
        $query = Category::with(['parent', 'children'])
            ->withCount(['variants', 'listings']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('slug', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            if ($request->parent_id === 'root') {
                $query->whereNull('parent_id');
            } elseif ($request->parent_id !== '') {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $categories = $query->orderBy('level')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        // For AJAX requests (used by filter/search)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.categories.partials.table', compact('categories'))->render(),
                'pagination' => $categories->links()->render(),
            ]);
        }

        // Get parent categories for filter dropdown
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
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

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['slug'] = $this->generateUniqueSlug($validated['slug']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        if (! empty($validated['parent_id'])) {
            $parent = Category::findOrFail($validated['parent_id']);
            $validated['level'] = $parent->level + 1;
            $validated['path'] = $parent->path ? $parent->path.'/'.$parent->id : (string) $parent->id;
        } else {
            $validated['level'] = 1;
            $validated['path'] = null;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category = Category::create($validated);

        activity()
            ->performedOn($category)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Category created');

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Get available variants for category (AJAX)
     */
    public function getAvailableVariants(Category $category)
    {
        $allVariants = Variant::with('items')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $assignedVariantIds = $category->variants()->pluck('variants.id')->toArray();

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'level' => $category->level,
            ],
            'variants' => $allVariants->map(function ($variant) use ($assignedVariantIds) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'type' => $variant->type,
                    'items_count' => $variant->items->count(),
                    'is_assigned' => in_array($variant->id, $assignedVariantIds),
                    'current_settings' => $variant->categoryVariants()
                        ->where('category_id', request()->route('category')->id)
                        ->first(),
                ];
            }),
        ]);
    }

    /**
     * Sync variants to category (AJAX)
     */
    public function syncVariants(Request $request, Category $category)
    {
        $validated = $request->validate([
            'variants' => 'required|array',
            'variants.*.id' => 'required|exists:variants,id',
            'variants.*.is_required' => 'boolean',
            'variants.*.is_searchable' => 'boolean',
            'variants.*.is_filterable' => 'boolean',
            'variants.*.sort_order' => 'integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Detach all existing variants
            $category->variants()->detach();

            // Attach selected variants with pivot data
            foreach ($validated['variants'] as $variantData) {
                $category->variants()->attach($variantData['id'], [
                    'is_required' => $variantData['is_required'] ?? false,
                    'is_searchable' => $variantData['is_searchable'] ?? true,
                    'is_filterable' => $variantData['is_filterable'] ?? true,
                    'sort_order' => $variantData['sort_order'] ?? 0,
                ]);
            }

            DB::commit();

            activity()
                ->performedOn($category)
                ->causedBy(auth()->guard('admin')->user())
                ->withProperties([
                    'variant_count' => count($validated['variants']),
                    'variant_ids' => array_column($validated['variants'], 'id'),
                ])
                ->log('Category variants updated');

            return response()->json([
                'success' => true,
                'message' => 'Variants updated successfully',
                'variant_count' => count($validated['variants']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating variants: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get children for AJAX (used in create form)
     */
    public function getChildren(Request $request, ?int $categoryId = null)
    {
        $query = Category::with('children')
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

    /**
     * Format category for select dropdown (recursive)
     */
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

    /**
     * Generate unique slug
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
 * Get direct children of a category (AJAX)
 */
public function getDirectChildren(Request $request, ?Category $category = null)
{
    $query = Category::withCount(['variants', 'listings', 'children'])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name');

    if ($category) {
        $query->where('parent_id', $category->id);
        $parentName = $category->name;
    } else {
        $query->whereNull('parent_id');
        $parentName = null;
    }

    $categories = $query->get();

    return response()->json([
        'success' => true,
        'parent_name' => $parentName,
        'parent_id' => $category?->id,
        'categories' => $categories->map(function($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'icon' => $cat->icon,
                'is_active' => $cat->is_active,
                'variants_count' => $cat->variants_count,
                'listings_count' => $cat->listings_count,
                'has_children' => $cat->children_count > 0,
            ];
        }),
    ]);
}
}
