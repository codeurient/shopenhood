<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryVariantController extends Controller
{
    /**
     * Get available variants for a category (AJAX)
     */
    public function index(Category $category)
    {
        Log::info('📖 Loading variants for category', [
            'category_id' => $category->id,
            'category_name' => $category->name,
        ]);

        $allVariants = Variant::with('items')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $assignedVariants = $category->variants()
            ->get()
            ->keyBy('id');

        Log::info('📊 Assigned variants loaded', [
            'count' => $assignedVariants->count(),
            'variant_ids' => $assignedVariants->keys()->toArray(),
        ]);

        $response = [
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'level' => $category->level,
            ],
            'variants' => $allVariants->map(function ($variant) use ($assignedVariants) {
                $isAssigned = $assignedVariants->has($variant->id);
                $pivotData = $isAssigned ? $assignedVariants->get($variant->id)->pivot : null;

                $settings = $pivotData ? [
                    'is_required' => (bool) $pivotData->is_required,
                    'is_searchable' => (bool) $pivotData->is_searchable,
                    'is_filterable' => (bool) $pivotData->is_filterable,
                    'is_main_shown' => (bool) $pivotData->is_main_shown,
                    'sort_order' => (int) $pivotData->sort_order,
                ] : null;

                // Log each assigned variant's settings
                if ($isAssigned) {
                    Log::info("  Variant '{$variant->name}' (ID: {$variant->id})", [
                        'settings' => $settings,
                    ]);
                }

                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'type' => $variant->type,
                    'items_count' => $variant->items->count(),
                    'is_assigned' => $isAssigned,
                    'settings' => $settings,
                ];
            }),
        ];

        return response()->json($response);
    }

    /**
     * Sync variants to category (AJAX)
     */
    public function sync(Request $request, Category $category)
    {

        Log::info('🔄 Syncing variants for category', [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'request_data' => $request->all(),
        ]);

        $validated = $request->validate([
            'variants' => 'required|array',
            'variants.*.id' => 'required|exists:variants,id',
            'variants.*.is_required' => 'boolean',
            'variants.*.is_searchable' => 'boolean',
            'variants.*.is_filterable' => 'boolean',
            'variants.*.is_main_shown' => 'boolean',
            'variants.*.sort_order' => 'integer|min:0',
        ]);

        // dd($validated['variants'] );

        Log::info('✓ Validation passed', [
            'validated_variants' => $validated['variants'],
        ]);

        DB::beginTransaction();
        try {
            // Detach all existing variants
            $category->variants()->detach();

            // Attach selected variants with pivot data
            foreach ($validated['variants'] as $variantData) {
                $pivotData = [
                    'is_required' => $variantData['is_required'] ?? false,
                    'is_searchable' => $variantData['is_searchable'] ?? true,
                    'is_filterable' => $variantData['is_filterable'] ?? true,
                    'is_main_shown' => $variantData['is_main_shown'] ?? false,
                    'sort_order' => $variantData['sort_order'] ?? 0,
                ];

                Log::info('📌 Attaching variant', [
                    'variant_id' => $variantData['id'],
                    'pivot_data' => $pivotData,
                ]);

                $category->variants()->attach($variantData['id'], $pivotData);
            }

            DB::commit();

            Log::info('✓ Variants saved successfully');

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
                'message' => 'Error updating variants: '.$e->getMessage(),
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
            'is_main_shown' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category->variants()->updateExistingPivot($variant->id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
        ]);
    }
}
