<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /** GET /my-orders — Buyer's purchase history. */
    public function index(Request $request): View
    {
        $purchases = Purchase::forBuyer(Auth::id())
            ->with(['orders.listing.primaryImage', 'orders.listing.firstImage'])
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('purchases'));
    }

    /** GET /my-orders/{purchase} — Single purchase detail. */
    public function show(Purchase $purchase): View
    {
        abort_if($purchase->buyer_id !== Auth::id(), 403);

        $purchase->load([
            'orders.listing.primaryImage',
            'orders.listing.firstImage',
            'orders.listing.user',
            'orders.variation',
        ]);

        // Group orders by seller for display
        $ordersBySeller = $purchase->orders->groupBy(fn ($order) => $order->seller_id);

        return view('user.orders.show', compact('purchase', 'ordersBySeller'));
    }
}
