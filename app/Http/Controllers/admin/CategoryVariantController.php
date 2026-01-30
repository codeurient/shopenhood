<?php

namespace App\Http\Controllers\Admin;

use App\Models\Variant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CategoryVariantController extends Controller
{
    /**
     * Get available variants for a category (AJAX)
     */
    public function index(Category $category)
    {
        $allVariants = Variant::with('items')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $assignedVariants = $category->variants()
            ->get()
            ->keyBy('id');

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'level' => $category->level,
            ],
            'variants' => $allVariants->map(function($variant) use ($assignedVariants) {
                $isAssigned = $assignedVariants->has($variant->id);
                $pivotData = $isAssigned ? $assignedVariants->get($variant->id)->pivot : null;
                
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'type' => $variant->type,
                    'items_count' => $variant->items->count(),
                    'is_assigned' => $isAssigned,
                    'settings' => $pivotData ? [
                        'is_required' => (bool) $pivotData->is_required,
                        'is_searchable' => (bool) $pivotData->is_searchable,
                        'is_filterable' => (bool) $pivotData->is_filterable,
                        'sort_order' => (int) $pivotData->sort_order,
                    ] : null,
                ];
            }),
        ]);
    }

    /**
     * Sync variants to category (AJAX)
     */
    public function sync(Request $request, Category $category)
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
            
            Log::error('Category variant sync failed', [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating variants: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detach a variant from category
     */
    public function detach(Category $category, Variant $variant)
    {
        $category->variants()->detach($variant->id);

        activity()
            ->performedOn($category)
            ->causedBy(auth()->guard('admin')->user())
            ->withProperties(['variant_id' => $variant->id])
            ->log('Variant detached from category');

        return response()->json([
            'success' => true,
            'message' => 'Variant removed successfully',
        ]);
    }

    /**
     * Update pivot settings for a specific variant
     */
    public function updatePivot(Request $request, Category $category, Variant $variant)
    {
        $validated = $request->validate([
            'is_required' => 'boolean',
            'is_searchable' => 'boolean',
            'is_filterable' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category->variants()->updateExistingPivot($variant->id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
        ]);
    }

    
}