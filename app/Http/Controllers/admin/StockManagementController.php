<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ProductVariation;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockManagementController extends Controller
{
    /**
     * Display the stock management dashboard
     */
    public function index(Request $request)
    {
        $query = ProductVariation::with([
            'listing:id,title,slug',
            'listing.category:id,name',
            'attributes.variant',
            'attributes.variantItem',
        ])
            ->where('manage_stock', true)
            ->where('is_active', true);

        // Filter by stock status
        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->where('stock_quantity', '>', 0)
                        ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
                    break;
                case 'out':
                    $query->where('stock_quantity', '<=', 0);
                    break;
                case 'in':
                    $query->where('stock_quantity', '>', 0);
                    break;
            }
        }

        // Search by SKU or listing title
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhereHas('listing', function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'stock_quantity');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $variations = $query->paginate(20);

        // Get stock statistics
        $stats = [
            'total_variations' => ProductVariation::where('manage_stock', true)->count(),
            'low_stock_count' => ProductVariation::where('manage_stock', true)
                ->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->count(),
            'out_of_stock_count' => ProductVariation::where('manage_stock', true)
                ->where('stock_quantity', '<=', 0)
                ->count(),
            'total_stock_value' => ProductVariation::where('manage_stock', true)
                ->selectRaw('SUM(stock_quantity * price) as total')
                ->value('total') ?? 0,
        ];

        return view('admin.stock.index', compact('variations', 'stats'));
    }

    /**
     * Show stock adjustment form for a variation
     */
    public function edit(ProductVariation $variation)
    {
        $variation->load([
            'listing:id,title,slug',
            'attributes.variant',
            'attributes.variantItem',
            'stockMovements' => function ($query) {
                $query->latest('created_at')->limit(10);
            },
            'stockMovements.user:id,name',
        ]);

        return view('admin.stock.edit', compact('variation'));
    }

    /**
     * Adjust stock for a variation
     */
    public function adjust(Request $request, ProductVariation $variation)
    {
        $validated = $request->validate([
            'type' => 'required|in:purchase,sale,return,adjustment,damage',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // For sale and damage, quantity should be negative (removed from stock)
            // For purchase and return, quantity should be positive (added to stock)
            // For adjustment, respect the sign entered by user
            $quantity = $validated['quantity'];

            if (in_array($validated['type'], ['sale', 'damage'])) {
                $quantity = -abs($quantity); // Make it negative
            } elseif (in_array($validated['type'], ['purchase', 'return'])) {
                $quantity = abs($quantity); // Make it positive
            }
            // For 'adjustment', keep the original sign

            // Adjust stock using model method
            $variation->adjustStock(
                $quantity,
                $validated['type'],
                $validated['reference'] ?? null,
                $validated['notes'] ?? null
            );

            DB::commit();

            return redirect()
                ->route('admin.stock.edit', $variation)
                ->with('success', 'Stock adjusted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to adjust stock: '.$e->getMessage());
        }
    }

    /**
     * Display stock movement history
     */
    public function history(Request $request)
    {
        $query = StockMovement::with([
            'productVariation:id,sku,listing_id',
            'productVariation.listing:id,title',
            'user:id,name',
        ])->latest('created_at');

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by SKU or reference
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('productVariation', function ($q) use ($search) {
                        $q->where('sku', 'like', "%{$search}%");
                    });
            });
        }

        $movements = $query->paginate(50);

        return view('admin.stock.history', compact('movements'));
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts()
    {
        $lowStockVariations = ProductVariation::with([
            'listing:id,title,slug',
            'listing.category:id,name',
            'attributes.variant',
            'attributes.variantItem',
        ])
            ->where('manage_stock', true)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return view('admin.stock.low-stock-alerts', compact('lowStockVariations'));
    }

    /**
     * Bulk stock update
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'variations' => 'required|array',
            'variations.*.id' => 'required|exists:product_variations,id',
            'variations.*.quantity' => 'required|integer|min:1',
            'type' => 'required|in:purchase,sale,return,adjustment,damage',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['variations'] as $variationData) {
                $variation = ProductVariation::find($variationData['id']);

                // Apply sign based on transaction type
                $quantity = $variationData['quantity'];

                if (in_array($validated['type'], ['sale', 'damage'])) {
                    $quantity = -abs($quantity);
                } elseif (in_array($validated['type'], ['purchase', 'return'])) {
                    $quantity = abs($quantity);
                }

                $variation->adjustStock(
                    $quantity,
                    $validated['type'],
                    $validated['reference'] ?? null,
                    $validated['notes'] ?? null
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.stock.index')
                ->with('success', 'Bulk stock update completed successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Bulk update failed: '.$e->getMessage());
        }
    }

    /**
     * Export stock report as CSV
     */
    public function export(Request $request)
    {
        $variations = ProductVariation::with([
            'listing:id,title',
            'attributes.variant',
            'attributes.variantItem',
        ])
            ->where('manage_stock', true)
            ->get();

        $filename = 'stock-report-'.now()->format('Y-m-d-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($variations) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'SKU',
                'Product',
                'Variant',
                'Price',
                'Stock Quantity',
                'Low Stock Threshold',
                'Status',
                'Stock Value',
            ]);

            // CSV Data
            foreach ($variations as $variation) {
                $variantDescription = $variation->attributes->map(function ($attr) {
                    return $attr->variant->name.': '.$attr->variantItem->value;
                })->implode(', ');

                $status = $variation->stock_quantity <= 0
                    ? 'Out of Stock'
                    : ($variation->stock_quantity <= $variation->low_stock_threshold ? 'Low Stock' : 'In Stock');

                fputcsv($file, [
                    $variation->sku,
                    $variation->listing->title,
                    $variantDescription,
                    number_format($variation->price, 2),
                    $variation->stock_quantity,
                    $variation->low_stock_threshold,
                    $status,
                    number_format($variation->stock_quantity * $variation->price, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
