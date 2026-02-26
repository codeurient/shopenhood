<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderCannotShipNotification;
use App\Notifications\OrderShippedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesController extends Controller
{
    /** GET /my-sales — Seller's incoming orders. */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $query = Order::query()
            ->where('seller_id', Auth::id())
            ->with([
                'listing.primaryImage',
                'listing.firstImage',
                'variation',
                'buyer',
                'purchase',
            ])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15)->appends($request->only('status'));

        $baseCounts = Order::where('seller_id', Auth::id())
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'all' => $baseCounts->sum(),
            'pending' => $baseCounts->get('pending', 0),
            'processing' => $baseCounts->get('processing', 0),
            'shipped' => $baseCounts->get('shipped', 0),
            'delivered' => $baseCounts->get('delivered', 0),
            'completed' => $baseCounts->get('completed', 0),
            'cancelled' => $baseCounts->get('cancelled', 0),
        ];

        return view('user.sales.index', compact('orders', 'status', 'counts'));
    }

    /** POST /my-sales/{order}/ship — Seller marks an order as shipped. */
    public function ship(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->seller_id !== Auth::id(), 403);
        abort_if(! $order->canBeShipped(), 422);

        $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $order->markAsShipped($request->input('tracking_number'));

        if ($order->buyer) {
            $order->buyer->notify(new OrderShippedNotification($order));
        }

        return back()->with('success', 'Order marked as shipped.');
    }

    /** POST /my-sales/{order}/deliver — Seller confirms order was delivered. */
    public function deliver(Order $order): RedirectResponse
    {
        abort_if($order->seller_id !== Auth::id(), 403);
        abort_if(! $order->canBeMarkedDelivered(), 422);

        $order->markAsDelivered();

        return back()->with('success', 'Order marked as delivered.');
    }

    /** POST /my-sales/{order}/cannot-ship — Seller cancels order with reason. */
    public function cannotShip(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->seller_id !== Auth::id(), 403);
        abort_if(! $order->canBeShipped(), 422);

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $order->markAsCancelled($request->input('reason'));

        if ($order->buyer) {
            $order->buyer->notify(new OrderCannotShipNotification($order));
        }

        return back()->with('success', 'Order cancelled and buyer has been notified.');
    }
}
