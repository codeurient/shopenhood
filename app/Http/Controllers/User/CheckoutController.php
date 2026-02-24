<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmOrderRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * GET /checkout
     * Full checkout page — server-side rendered with selected cart items.
     */
    public function index(): View|RedirectResponse
    {
        $items = CartItem::query()
            ->where('user_id', Auth::id())
            ->where('is_selected', true)
            ->with(['listing.user', 'listing.primaryImage', 'listing.firstImage', 'variation'])
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('home')->with('warning', 'No items selected for checkout.');
        }

        $grouped = $items->groupBy(fn (CartItem $item) => $item->listing->user_id ?? 0);

        $sellers = $grouped->map(function ($sellerItems, $sellerId) {
            /** @var CartItem $first */
            $first = $sellerItems->first();
            $sellerName = $first->listing?->user?->name ?? 'Unknown Seller';
            $deliveryOptions = $this->deriveDeliveryOptions($sellerItems);

            $formattedItems = $sellerItems->map(fn (CartItem $item) => [
                'listing_id' => $item->listing_id,
                'variation_id' => $item->variation_id,
                'title' => $item->listing?->title ?? 'Deleted listing',
                'image_url' => $item->listing?->primaryImage
                    ? asset('storage/'.$item->listing->primaryImage->image_path)
                    : ($item->listing?->firstImage
                        ? asset('storage/'.$item->listing->firstImage->image_path)
                        : null),
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
                'currency' => $item->listing?->currency ?? 'USD',
            ]);

            return [
                'seller_id' => $sellerId,
                'seller_name' => $sellerName,
                'items' => $formattedItems->values()->toArray(),
                'delivery_options' => $deliveryOptions,
                'selected_delivery' => $deliveryOptions[0]['key'] ?? 'pickup',
            ];
        })->values()->toArray();

        $addresses = UserAddress::forUser(Auth::id())->orderByDesc('is_default')->get();
        $defaultAddressId = $addresses->firstWhere('is_default', true)?->id ?? $addresses->first()?->id;

        $subtotal = round($items->sum(fn (CartItem $item) => $item->line_total), 2);
        $couponTotal = round($items->sum(fn (CartItem $item) => $item->coupon_discount ?? 0), 2);
        $itemCount = $items->sum('quantity');
        $currency = $items->first()?->listing?->currency ?? 'USD';

        return view('checkout.index', compact(
            'sellers', 'addresses', 'defaultAddressId', 'subtotal', 'couponTotal', 'itemCount', 'currency'
        ));
    }

    /**
     * GET /api/checkout/prepare
     * Returns selected cart items grouped by seller, user addresses, and totals.
     */
    public function prepare(): JsonResponse
    {
        $items = CartItem::query()
            ->where('user_id', Auth::id())
            ->where('is_selected', true)
            ->with([
                'listing.user',
                'listing.primaryImage',
                'listing.firstImage',
                'variation',
            ])
            ->get();

        if ($items->isEmpty()) {
            return response()->json(['message' => 'No items selected for checkout.'], 422);
        }

        // Group items by seller
        $grouped = $items->groupBy(fn (CartItem $item) => $item->listing->user_id ?? 0);

        $sellers = $grouped->map(function ($sellerItems, $sellerId) {
            /** @var CartItem $first */
            $first = $sellerItems->first();
            $sellerName = $first->listing?->user?->name ?? 'Unknown Seller';

            // Derive delivery options from listings
            $deliveryOptions = $this->deriveDeliveryOptions($sellerItems);

            $formattedItems = $sellerItems->map(fn (CartItem $item) => [
                'id' => $item->id,
                'listing_id' => $item->listing_id,
                'variation_id' => $item->variation_id,
                'title' => $item->listing?->title ?? 'Deleted listing',
                'image_url' => $item->listing?->primaryImage
                    ? asset('storage/'.$item->listing->primaryImage->image_path)
                    : ($item->listing?->firstImage
                        ? asset('storage/'.$item->listing->firstImage->image_path)
                        : null),
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total,
                'currency' => $item->listing?->currency ?? 'USD',
            ]);

            return [
                'seller_id' => $sellerId,
                'seller_name' => $sellerName,
                'items' => $formattedItems->values(),
                'delivery_options' => $deliveryOptions,
            ];
        })->values();

        // Addresses
        $addresses = UserAddress::forUser(Auth::id())
            ->orderByDesc('is_default')
            ->get()
            ->map(fn (UserAddress $addr) => [
                'id' => $addr->id,
                'label' => $addr->label,
                'is_default' => $addr->is_default,
                'recipient_name' => $addr->recipient_name,
                'phone' => $addr->phone,
                'full_address' => $addr->full_address,
                'street' => $addr->street,
                'city' => $addr->city,
                'country' => $addr->country,
                'postal_code' => $addr->postal_code,
            ]);

        $defaultAddress = $addresses->firstWhere('is_default', true);

        $subtotal = $items->sum(fn (CartItem $item) => $item->line_total);
        $currency = $items->first()?->listing?->currency ?? 'USD';

        return response()->json([
            'sellers' => $sellers,
            'addresses' => $addresses,
            'default_address_id' => $defaultAddress['id'] ?? $addresses->first()['id'] ?? null,
            'totals' => [
                'subtotal' => round($subtotal, 2),
                'item_count' => $items->sum('quantity'),
                'currency' => $currency,
            ],
        ]);
    }

    /**
     * POST /checkout/confirm
     * Creates a Purchase + one Order per selected cart item.
     */
    public function confirm(ConfirmOrderRequest $request): JsonResponse|RedirectResponse
    {
        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $items = CartItem::query()
            ->where('user_id', Auth::id())
            ->where('is_selected', true)
            ->with(['listing.user', 'variation'])
            ->get();

        if ($items->isEmpty()) {
            return response()->json(['message' => 'No items selected for checkout.'], 422);
        }

        $currency = $items->first()?->listing?->currency ?? 'USD';
        $deliverySelections = $request->delivery_selections ?? [];

        $purchase = DB::transaction(function () use ($items, $address, $request, $currency, $deliverySelections) {
            $subtotal = 0;
            $totalShipping = 0;

            $purchase = Purchase::create([
                'purchase_number' => Purchase::generatePurchaseNumber(),
                'buyer_id' => Auth::id(),
                'address_snapshot' => $address->toOrderSnapshot(),
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'subtotal' => 0,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'currency' => $currency,
                'status' => 'pending',
            ]);

            foreach ($items as $cartItem) {
                $sellerId = $cartItem->listing?->user_id;
                $sellerKey = (string) $sellerId;

                // Resolve selected delivery option for this seller
                $selectedDelivery = $deliverySelections[$sellerKey] ?? null;
                [$deliveryOptionName, $shippingCost, $deliveryCostPaidBy] = $this->resolveDeliveryOption(
                    $selectedDelivery,
                    $cartItem
                );

                $lineTotal = $cartItem->line_total;
                $subtotal += $lineTotal;
                $totalShipping += $shippingCost;

                Order::create([
                    'purchase_id' => $purchase->id,
                    'order_number' => $this->generateOrderNumber(),
                    'buyer_id' => Auth::id(),
                    'seller_id' => $sellerId,
                    'listing_id' => $cartItem->listing_id,
                    'variation_id' => $cartItem->variation_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'subtotal' => $lineTotal,
                    'shipping_cost' => $shippingCost,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $lineTotal + $shippingCost,
                    'currency' => $cartItem->listing?->currency ?? $purchase->currency,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $purchase->payment_method,
                    'delivery_option_name' => $deliveryOptionName,
                    'delivery_cost_paid_by' => $deliveryCostPaidBy,
                ]);
            }

            // Update purchase totals
            $purchase->update([
                'subtotal' => round($subtotal, 2),
                'shipping_cost' => round($totalShipping, 2),
                'total_amount' => round($subtotal + $totalShipping, 2),
            ]);

            // Remove selected cart items
            CartItem::where('user_id', Auth::id())
                ->where('is_selected', true)
                ->delete();

            return $purchase;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'purchase_number' => $purchase->purchase_number,
                'redirect_url' => route('user.purchases.show', $purchase),
            ]);
        }

        return redirect()->route('user.purchases.show', $purchase)
            ->with('success', 'Your order has been placed successfully!');
    }

    /**
     * Derive available delivery options from a group of cart items (same seller).
     *
     * @param  \Illuminate\Support\Collection<int, CartItem>  $sellerItems
     * @return array<int, array<string, mixed>>
     */
    private function deriveDeliveryOptions($sellerItems): array
    {
        $options = [];

        // Check listing delivery settings
        $hasFreeDelivery = false;
        $totalPaidDelivery = 0.0;
        $hasPaidDelivery = false;

        foreach ($sellerItems as $item) {
            $listing = $item->listing;
            if (! $listing) {
                continue;
            }

            if ($listing->has_domestic_delivery) {
                $price = (float) $listing->domestic_delivery_price;
                if ($price <= 0) {
                    $hasFreeDelivery = true;
                } else {
                    $hasPaidDelivery = true;
                    $totalPaidDelivery += $price;
                }
            }
        }

        if ($hasFreeDelivery) {
            $options[] = [
                'key' => 'free_shipping',
                'name' => 'Free Shipping',
                'cost' => 0.0,
                'paid_by' => 'seller',
                'note' => null,
            ];
        }

        if ($hasPaidDelivery) {
            $options[] = [
                'key' => 'standard_delivery',
                'name' => 'Standard Delivery',
                'cost' => $totalPaidDelivery,
                'paid_by' => 'buyer',
                'note' => 'Delivery cost to be paid directly to the seller.',
            ];
        }

        return $options;
    }

    /**
     * @return array{string, float, string} [name, shipping_cost, paid_by]
     */
    private function resolveDeliveryOption(?string $optionKey, CartItem $cartItem): array
    {
        $listing = $cartItem->listing;

        switch ($optionKey) {
            case 'free_shipping':
                return ['Free Shipping', 0.0, 'seller'];

            case 'standard_delivery':
                $cost = $listing ? (float) $listing->domestic_delivery_price : 0.0;

                return ['Standard Delivery', $cost, 'buyer'];

            default:
                return ['Pickup / Arrange with Seller', 0.0, 'seller'];
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.date('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
