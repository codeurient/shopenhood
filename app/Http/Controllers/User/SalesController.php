<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        $counts = [
            'all' => Order::where('seller_id', Auth::id())->count(),
            'pending' => Order::where('seller_id', Auth::id())->where('status', 'pending')->count(),
            'processing' => Order::where('seller_id', Auth::id())->where('status', 'processing')->count(),
            'completed' => Order::where('seller_id', Auth::id())->where('status', 'completed')->count(),
        ];

        return view('user.sales.index', compact('orders', 'status', 'counts'));
    }
}
