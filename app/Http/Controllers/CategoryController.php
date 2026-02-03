<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Variant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function getChildren(?Category $category = null): JsonResponse
    {
        $query = Category::where('is_active', true)
            ->withCount('children')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($category) {
            $query->where('parent_id', $category->id);
        } else {
            $query->whereNull('parent_id');
        }

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'categories' => $categories->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'has_children' => $cat->children_count > 0,
                ];
            }),
        ]);
    }

    public function getVariants(Request $request, Category $category): JsonResponse
    {
        try {
            $showAll = $request->query('show_all', false);

            $variants = Variant::whereHas('categories', function ($query) use ($category, $showAll) {
                $query->where('categories.id', $category->id);

                if (! $showAll) {
                    $query->where('category_variants.is_main_shown', true);
                }
            })
                ->with([
                    'items' => function ($query) {
                        $query->where('is_active', true)->orderBy('sort_order');
                    },
                    'categories' => function ($query) use ($category) {
                        $query->where('categories.id', $category->id);
                    },
                ])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
                'variants' => $variants->map(function ($variant) {
                    $categoryRelation = $variant->categories->first();
                    $pivot = $categoryRelation ? $categoryRelation->pivot : null;

                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'type' => $variant->type,
                        'is_required' => $pivot->is_required ?? false,
                        'is_main_shown' => $pivot->is_main_shown ?? false,
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

    public function getHierarchy(Category $category): JsonResponse
    {
        $hierarchy = [];
        $current = $category;

        while ($current) {
            array_unshift($hierarchy, [
                'id' => $current->id,
                'name' => $current->name,
                'parent_id' => $current->parent_id,
            ]);

            $current = $current->parent;
        }

        return response()->json([
            'success' => true,
            'hierarchy' => $hierarchy,
        ]);
    }
}
