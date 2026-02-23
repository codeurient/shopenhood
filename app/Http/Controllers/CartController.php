<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductVariation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /** GET /api/cart — Return all cart items for the authenticated user. */
    public function index(): JsonResponse
    {
        $items = CartItem::query()
            ->where('user_id', Auth::id())
            ->with([
                'listing.primaryImage',
                'listing.firstImage',
                'listing.user',
                'listing.defaultVariation.primaryImage',
                'listing.defaultVariation.firstImage',
                'variation.primaryImage',
                'variation.firstImage',
            ])
            ->latest()
            ->get();

        $data = $items->map(fn (CartItem $item) => $this->formatItem($item));

        $selectedItems = $items->where('is_selected', true);
        $total = $selectedItems->sum(fn (CartItem $item) => $item->line_total);

        return response()->json([
            'items' => $data,
            'total' => round($total, 2),
            'count' => $items->count(),
            'selected_count' => $selectedItems->count(),
        ]);
    }

    /** POST /api/cart — Add or increment a listing in the cart. */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'listing_id' => ['required', 'integer', 'exists:listings,id'],
            'variation_id' => ['nullable', 'integer', 'exists:product_variations,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $cartItem = CartItem::firstOrNew([
            'user_id' => Auth::id(),
            'listing_id' => $request->listing_id,
            'variation_id' => $request->variation_id,
        ]);

        $addQty = $request->quantity ?? 1;
        $existingQty = $cartItem->exists ? $cartItem->quantity : 0;
        $newQty = $existingQty + $addQty;

        if ($request->variation_id) {
            $variation = ProductVariation::find($request->variation_id);
            if ($variation && $variation->manage_stock && ! $variation->allow_backorder) {
                if ($newQty > $variation->stock_quantity) {
                    return response()->json([
                        'message' => 'Only '.$variation->stock_quantity.' item(s) available in stock.',
                    ], 422);
                }
            }
        }

        $cartItem->quantity = $newQty;
        $cartItem->is_selected = true;
        $cartItem->save();

        return response()->json([
            'message' => 'Added to cart',
            'count' => CartItem::where('user_id', Auth::id())->count(),
        ]);
    }

    /** PATCH /api/cart/{cartItem} — Update quantity or selected state. */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        $this->authorizeItem($cartItem);

        $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
            'is_selected' => ['sometimes', 'boolean'],
        ]);

        $cartItem->fill($request->only(['quantity', 'is_selected']))->save();

        return response()->json(['message' => 'Updated']);
    }

    /** DELETE /api/cart/{cartItem} — Remove a single item. */
    public function destroy(CartItem $cartItem): JsonResponse
    {
        $this->authorizeItem($cartItem);
        $cartItem->delete();

        return response()->json([
            'message' => 'Removed',
            'count' => CartItem::where('user_id', Auth::id())->count(),
        ]);
    }

    /** DELETE /api/cart — Clear all items for the user. */
    public function clear(): JsonResponse
    {
        CartItem::where('user_id', Auth::id())->delete();

        return response()->json(['message' => 'Cart cleared', 'count' => 0]);
    }

    /** POST /api/cart/select-all — Toggle is_selected for all items. */
    public function selectAll(Request $request): JsonResponse
    {
        $request->validate(['selected' => ['required', 'boolean']]);

        CartItem::where('user_id', Auth::id())
            ->update(['is_selected' => $request->boolean('selected')]);

        return response()->json(['message' => 'Updated']);
    }

    /** DELETE /api/cart/selected — Remove all selected items. */
    public function destroySelected(): JsonResponse
    {
        CartItem::where('user_id', Auth::id())
            ->where('is_selected', true)
            ->delete();

        return response()->json([
            'message' => 'Selected items removed',
            'count' => CartItem::where('user_id', Auth::id())->count(),
        ]);
    }

    private function authorizeItem(CartItem $cartItem): void
    {
        abort_if($cartItem->user_id !== Auth::id(), 403);
    }

    private function formatItem(CartItem $item): array
    {
        $listing = $item->listing;
        $variation = $item->variation ?? $listing?->defaultVariation;
        $imagePath = $listing?->primaryImage?->image_path
            ?? $listing?->firstImage?->image_path
            ?? $variation?->primaryImage?->image_path
            ?? $variation?->firstImage?->image_path;
        $image = $imagePath ? asset('storage/'.$imagePath) : null;

        $hasListingDiscount = $listing?->discount_price &&
            $listing->discount_start_date <= now() &&
            $listing->discount_end_date >= now();

        // Resolve base price and discount price (listing-level first, variation as fallback)
        $basePrice = (float) ($listing?->base_price ?? 0);
        $discountPrice = null;

        if ($hasListingDiscount) {
            $discountPrice = (float) $listing->discount_price;
        } elseif (! $basePrice) {
            if ($variation) {
                $basePrice = (float) $variation->price;
                if ($variation->hasActiveDiscount()) {
                    $discountPrice = (float) $variation->discount_price;
                }
            }
        }

        $maxQty = null;
        if ($variation && $variation->manage_stock && ! $variation->allow_backorder) {
            $maxQty = max(0, $variation->stock_quantity);
        }

        return [
            'id' => $item->id,
            'listing_id' => $item->listing_id,
            'variation_id' => $item->variation_id,
            'quantity' => $item->quantity,
            'is_selected' => $item->is_selected,
            'title' => $listing?->title ?? 'Deleted listing',
            'seller_name' => $listing?->user?->name ?? '—',
            'image_url' => $image,
            'currency' => $listing?->currency ?? 'USD',
            'base_price' => $basePrice,
            'discount_price' => $discountPrice,
            'unit_price' => $item->unit_price,
            'line_total' => $item->line_total,
            'has_delivery' => (bool) $listing?->has_delivery,
            'delivery_cost' => $item->delivery_cost,
            'max_qty' => $maxQty,
        ];
    }
}
