<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = ProductVariation::with([
            'listing:id,title,slug,user_id',
            'attributes.variant',
            'attributes.variantItem',
        ])
            ->whereHas('listing', fn ($q) => $q->where('user_id', $user->id))
            ->where('manage_stock', true)
            ->where('is_active', true);

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->where('stock_quantity', '>', 0)
                        ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
                    break;
                case 'out':
                    $query->where('stock_quantity', '<=', 0);
                    break;
                case 'in':
                    $query->whereColumn('stock_quantity', '>', 'low_stock_threshold');
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhereHas('listing', fn ($q2) => $q2->where('title', 'like', "%{$search}%"));
            });
        }

        $sortBy = in_array($request->get('sort_by'), ['stock_quantity', 'sku', 'price']) ? $request->get('sort_by') : 'stock_quantity';
        $query->orderBy($sortBy, 'asc');

        $variations = $query->paginate(20)->withQueryString();

        $stats = [
            'total_variations' => ProductVariation::whereHas('listing', fn ($q) => $q->where('user_id', $user->id))
                ->where('manage_stock', true)->count(),
            'low_stock_count' => ProductVariation::whereHas('listing', fn ($q) => $q->where('user_id', $user->id))
                ->where('manage_stock', true)
                ->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->count(),
            'out_of_stock_count' => ProductVariation::whereHas('listing', fn ($q) => $q->where('user_id', $user->id))
                ->where('manage_stock', true)
                ->where('stock_quantity', '<=', 0)
                ->count(),
        ];

        return view('user.stock.index', compact('variations', 'stats'));
    }

    public function edit(ProductVariation $variation)
    {
        $this->authorizeVariation($variation);

        $variation->load([
            'listing:id,title,slug',
            'attributes.variant',
            'attributes.variantItem',
            'stockMovements' => fn ($q) => $q->latest('created_at')->limit(10),
        ]);

        return view('user.stock.edit', compact('variation'));
    }

    public function adjust(Request $request, ProductVariation $variation)
    {
        $this->authorizeVariation($variation);

        $validated = $request->validate([
            'type' => 'required|in:purchase,return,adjustment',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $quantity = abs($validated['quantity']);

            if ($validated['type'] === 'adjustment') {
                // For adjustments, allow the user to specify direction via sign convention
                $quantity = $validated['quantity'];
            }

            $variation->adjustStock(
                $quantity,
                $validated['type'],
                $validated['reference'] ?? null,
                $validated['notes'] ?? null,
            );

            DB::commit();

            return redirect()
                ->route('business.stock.edit', $variation)
                ->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()
                ->with('error', 'Failed to adjust stock: '.$e->getMessage());
        }
    }

    private function authorizeVariation(ProductVariation $variation): void
    {
        $variation->load('listing:id,user_id');

        abort_if($variation->listing->user_id !== auth()->id(), 403);
    }
}
